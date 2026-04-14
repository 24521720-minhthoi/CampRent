"""
Smart Chat Agent using Text-to-SQL approach.
LLM decides whether to use SQL query or vector search, eliminating the need for manual intent detection.
"""
import json
import re
from typing import Optional
from functools import lru_cache
from langchain_google_genai import ChatGoogleGenerativeAI
from langchain_core.prompts import ChatPromptTemplate
from langchain_core.output_parsers import StrOutputParser
from app.config import get_settings
from app.database import get_mysql_client
from app.vectorstore import get_vectorstore


# Database schema for LLM to understand
DATABASE_SCHEMA = """
Database Schema (MySQL):

1. products - Bảng sản phẩm cho thuê (đồ cắm trại, outdoor)
   - id (INT, PK)
   - name (VARCHAR) - Tên sản phẩm (VD: Lều 4 người, Bếp gas mini, Ba lô 60L)
   - slug (VARCHAR) - URL slug
   - description (TEXT) - Mô tả chi tiết
   - price (DECIMAL) - Giá thuê (VNĐ/ngày)
   - deposit_amount (DECIMAL) - Tiền đặt cọc (VNĐ)
   - stock (INT) - Số lượng còn lại
   - status (ENUM: 'available', 'rented', 'maintenance') - Trạng thái
   - category_id (INT, FK -> categories.id)
   - shop_id (INT, FK -> users.id) - Chủ shop
   - created_at, updated_at (TIMESTAMP)

2. categories - Danh mục sản phẩm outdoor
   - id (INT, PK)
   - name (VARCHAR) - Tên danh mục (VD: Lều & Tarp, Ba lô, Nấu nướng, Ánh sáng)
   - slug (VARCHAR)
   - description (TEXT)

3. orders - Đơn thuê
   - id (INT, PK)
   - user_id (INT, FK -> users.id) - Khách hàng
   - total_amount (DECIMAL) - Tổng tiền
   - status (ENUM: 'pending', 'confirmed', 'processing', 'shipped', 'delivered', 'returned', 'cancelled')
   - start_date, end_date (DATE) - Thời gian thuê
   - address (TEXT) - Địa chỉ giao hàng
   - created_at (TIMESTAMP)

4. order_items - Chi tiết đơn thuê
   - id (INT, PK)
   - order_id (INT, FK -> orders.id)
   - product_id (INT, FK -> products.id)
   - quantity (INT)
   - price (DECIMAL) - Giá tại thời điểm đặt

5. users - Người dùng
   - id (INT, PK)
   - name (VARCHAR)
   - email (VARCHAR)
   - role (ENUM: 'customer', 'shop', 'admin')

Common JOINs:
- products JOIN categories ON products.category_id = categories.id
- orders JOIN order_items ON orders.id = order_items.order_id
- order_items JOIN products ON order_items.product_id = products.id
"""


class SmartChatAgent:
    """
    Smart chat agent that uses LLM to decide query strategy:
    1. SQL Query - For structured data queries (price, stock, orders, statistics)
    2. Vector Search - For semantic/fuzzy product search (tìm đồ camping, leo núi, dã ngoại,...)
    """
    
    ROUTER_PROMPT = """Bạn là AI router cho hệ thống chatbot CampRent (nền tảng cho thuê đồ cắm trại & outdoor).
Nhiệm vụ: Phân tích câu hỏi của user và quyết định cách xử lý.

{schema}

Phân tích câu hỏi và trả về JSON với format:
{{
    "strategy": "sql" | "vector" | "conversation",
    "reasoning": "giải thích ngắn gọn tại sao chọn strategy này",
    "sql_query": "câu SQL nếu strategy=sql, null nếu không",
    "search_query": "từ khóa tìm kiếm nếu strategy=vector, null nếu không"
}}

Quy tắc chọn strategy:
- "sql": Khi cần query dữ liệu có cấu trúc (giá cao/thấp nhất, đếm số lượng, thống kê, tồn kho, đơn hàng cụ thể, filter theo điều kiện,...)
- "vector": Khi cần tìm sản phẩm theo ngữ nghĩa/mô tả (VD: "đi cắm trại 3 ngày", "leo núi mùa đông", "picnic gia đình", "camping bãi biển",...)
- "conversation": Chào hỏi, câu hỏi chung không liên quan đến data

Lưu ý SQL:
- Chỉ SELECT, không UPDATE/DELETE/INSERT
- Luôn LIMIT kết quả (max 10)
- Sử dụng CAST(price AS DECIMAL(15,2)) khi so sánh/sắp xếp giá
- WHERE status = 'available' cho sản phẩm có sẵn
- Nếu cần user_id, dùng placeholder {{user_id}}

Câu hỏi: {question}
User ID: {user_id}

Trả về JSON (không markdown):"""

    ANSWER_PROMPT = """Bạn là trợ lý ảo của CampRent — nền tảng cho thuê đồ cắm trại và outdoor hàng đầu Việt Nam.
Nhiệm vụ: Trả lời câu hỏi của khách hàng dựa trên dữ liệu được cung cấp.

Quy tắc trả lời:
- Tiếng Việt tự nhiên, thân thiện, phong cách năng động outdoor
- Format giá: 500.000₫/ngày (dấu chấm phân cách nghìn)
- Stock > 0: "còn hàng", stock = 0: "hết hàng"
- Nếu không có data phù hợp, gợi ý user mô tả chi tiết hơn về chuyến đi
- Nhắc về tiền đặt cọc nếu sản phẩm có deposit_amount > 0

Dữ liệu từ hệ thống:
{context}

Lịch sử hội thoại:
{chat_history}

Câu hỏi: {question}

Trả lời:"""

    def __init__(self):
        self.settings = get_settings()
        self._llm = None
        self._router_chain = None
        self._answer_chain = None
    
    def _get_llm(self, temperature: float = 0.7):
        """Get LLM instance."""
        if self._llm is None:
            self._llm = ChatGoogleGenerativeAI(
                model="gemini-2.5-flash",
                google_api_key=self.settings.google_api_key,
                temperature=temperature,
                max_output_tokens=2048,
            )
        return self._llm
    
    def _get_router_chain(self):
        """Chain for routing/SQL generation."""
        if self._router_chain is None:
            llm = ChatGoogleGenerativeAI(
                model="gemini-2.5-flash",
                google_api_key=self.settings.google_api_key,
                temperature=0.1,
                max_output_tokens=1024,
            )
            prompt = ChatPromptTemplate.from_messages([
                ("human", self.ROUTER_PROMPT)
            ])
            self._router_chain = prompt | llm | StrOutputParser()
        return self._router_chain
    
    def _get_answer_chain(self):
        """Chain for generating final answer."""
        if self._answer_chain is None:
            prompt = ChatPromptTemplate.from_messages([
                ("human", self.ANSWER_PROMPT)
            ])
            self._answer_chain = prompt | self._get_llm() | StrOutputParser()
        return self._answer_chain
    
    def _parse_router_response(self, response: str) -> dict:
        """Parse JSON response from router."""
        try:
            response = response.strip()
            if response.startswith("```"):
                response = re.sub(r"```json?\s*", "", response)
                response = re.sub(r"```\s*$", "", response)
            
            return json.loads(response)
        except json.JSONDecodeError:
            return {
                "strategy": "vector",
                "reasoning": "Failed to parse router response",
                "sql_query": None,
                "search_query": None
            }
    
    def _validate_sql(self, sql: str) -> bool:
        """Validate SQL query for safety."""
        sql = sql.strip().rstrip(";").strip()
        sql_upper = sql.upper()
        
        if not sql_upper.startswith("SELECT"):
            return False
        
        dangerous_patterns = [
            r"\bDROP\b", r"\bDELETE\b", r"\bUPDATE\b", r"\bINSERT\b", 
            r"\bALTER\b", r"\bTRUNCATE\b", r"\bEXEC\b", r"\bCREATE\b",
            r"--",
        ]
        for pattern in dangerous_patterns:
            if re.search(pattern, sql_upper):
                return False
        
        return True
    
    def _clean_sql(self, sql: str) -> str:
        """Clean SQL query before execution."""
        return sql.strip().rstrip(";").strip()
    
    def _execute_sql(self, sql: str, user_id: Optional[int] = None) -> list[dict]:
        """Execute SQL query safely."""
        sql = self._clean_sql(sql)
        
        if user_id and "{user_id}" in sql:
            sql = sql.replace("{user_id}", str(user_id))
        
        if not self._validate_sql(sql):
            raise ValueError("Invalid or unsafe SQL query")
        
        mysql_client = get_mysql_client()
        conn = mysql_client._get_connection()
        
        with conn.cursor() as cursor:
            cursor.execute(sql)
            return cursor.fetchall()
    
    def _format_sql_results(self, results: list[dict]) -> tuple[str, list[dict]]:
        """Format SQL results as context string."""
        if not results:
            return "Không tìm thấy dữ liệu phù hợp.", []
        
        context_parts = [f"Kết quả truy vấn ({len(results)} dòng):"]
        sources = []
        
        for i, row in enumerate(results, 1):
            row_parts = []
            for key, value in row.items():
                if key == "price" and value:
                    value = f"{float(value):,.0f}₫".replace(",", ".")
                row_parts.append(f"{key}: {value}")
            context_parts.append(f"{i}. " + " | ".join(row_parts))
            
            if "id" in row and "name" in row:
                sources.append({
                    "product_id": row.get("id"),
                    "name": row.get("name"),
                    "price": float(row["price"]) if row.get("price") else None,
                    "category": row.get("category_name")
                })
        
        return "\n".join(context_parts), sources
    
    def _format_vector_results(self, results: list[dict]) -> tuple[str, list[dict]]:
        """Format vector search results as context string."""
        if not results:
            return "Không tìm thấy sản phẩm outdoor phù hợp.", []
        
        context_parts = ["Sản phẩm outdoor phù hợp với yêu cầu:"]
        sources = []
        
        for product in results:
            price = product.get("price", 0)
            price_formatted = f"{price:,.0f}₫".replace(",", ".")
            context_parts.append(
                f"- {product['name']} | Giá: {price_formatted}/ngày | "
                f"Danh mục: {product.get('category', 'N/A')} | "
                f"Còn: {product.get('stock', 0)} sản phẩm"
            )
            sources.append({
                "product_id": product.get("product_id"),
                "name": product.get("name"),
                "price": price,
                "category": product.get("category")
            })
        
        return "\n".join(context_parts), sources
    
    def chat(
        self,
        query: str,
        user_id: Optional[int] = None,
        conversation_history: Optional[list[dict]] = None
    ) -> dict:
        """Process chat message using smart routing."""
        
        # Step 1: Route the query
        router_chain = self._get_router_chain()
        router_response = router_chain.invoke({
            "schema": DATABASE_SCHEMA,
            "question": query,
            "user_id": user_id or "không xác định"
        })
        
        routing = self._parse_router_response(router_response)
        strategy = routing.get("strategy", "vector")
        
        # Step 2: Execute based on strategy
        context = ""
        sources = []
        
        if strategy == "sql" and routing.get("sql_query"):
            try:
                sql = routing["sql_query"]
                results = self._execute_sql(sql, user_id)
                context, sources = self._format_sql_results(results)
            except Exception as e:
                print(f"[ERROR] SQL execution failed: {e}")
                strategy = "vector"
                routing["reasoning"] += f" (SQL failed: {e})"
        
        if strategy == "vector":
            search_query = routing.get("search_query") or query
            vectorstore = get_vectorstore()
            results = vectorstore.search_similar(search_query, n_results=5)
            context, sources = self._format_vector_results(results)
        
        if strategy == "conversation":
            context = "Đây là câu hỏi chung, không cần truy vấn dữ liệu. Hãy trả lời với phong cách thân thiện của CampRent."
        
        # Step 3: Format chat history
        chat_history = ""
        if conversation_history:
            for msg in conversation_history[-5:]:
                role = "Khách" if msg.get("role") == "user" else "Bot"
                chat_history += f"{role}: {msg.get('content', '')}\n"
        
        # Step 4: Generate final answer
        answer_chain = self._get_answer_chain()
        answer = answer_chain.invoke({
            "context": context,
            "chat_history": chat_history or "Chưa có hội thoại trước đó.",
            "question": query
        })
        
        return {
            "answer": answer,
            "sources": sources,
            "metadata": {
                "strategy": strategy,
                "reasoning": routing.get("reasoning"),
                "sql_query": routing.get("sql_query") if strategy == "sql" else None,
                "user_id": user_id
            }
        }


@lru_cache()
def get_smart_agent() -> SmartChatAgent:
    """Get cached SmartChatAgent instance."""
    return SmartChatAgent()
