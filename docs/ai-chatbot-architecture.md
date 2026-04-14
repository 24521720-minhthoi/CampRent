# CampRent AI Chatbot Service - Architecture Overview

## Kiến trúc hệ thống

Hệ thống chatbot AI của CampRent sử dụng kiến trúc **RAG (Retrieval-Augmented Generation)** với FastAPI + LangChain + ChromaDB để tư vấn sản phẩm outdoor cho thuê.

```mermaid
flowchart TB
    subgraph Client["🖥️ End-User Frontend"]
        A[Chat Widget]
    end

    subgraph Laravel["⚙️ Laravel Backend (Docker)"]
        B[ChatController]
        C[AIServiceClient]
    end

    subgraph AIService["🤖 CampRent AI Service (Python)"]
        D[FastAPI Server<br/>Port 8001]
        E[LangChain ChatAgent]
        F[Intent Detection]
        G[RAG Pipeline]
    end

    subgraph VectorDB["📦 Vector Database"]
        H[ChromaDB]
        I[Product Embeddings]
    end

    subgraph MySQL["🗄️ MySQL Database"]
        J[(Products)]
        K[(Orders)]
        L[(Categories)]
    end

    subgraph LLM["🧠 LLM Provider"]
        M[Google Gemini API]
    end

    A -->|"POST /api/chat"| B
    B --> C
    C -->|"POST /ask"| D
    D --> E
    E --> F
    F -->|"Product Search"| G
    F -->|"Order History"| J
    F -->|"Best Sellers"| J
    G --> H
    H --> I
    G --> M
    M -->|"Generated Response"| E
    E -->|"JSON Response"| C
    C --> B
    B -->|"Stream Response"| A

    J -->|"POST /sync"| H
```

---

## Chat Agent Modes

CampRent AI Service hỗ trợ 2 chế độ xử lý chat:

### 1. Smart Agent (Text-to-SQL) - Mặc định

LLM tự động quyết định chiến lược xử lý dựa trên câu hỏi khách hàng.

**Ví dụ queries:**
- "Lều nào rẻ nhất?" → SQL: `ORDER BY price ASC LIMIT 1`
- "Có bao nhiêu ba lô?" → SQL: `SELECT COUNT(*) WHERE category = 'Ba lô'`
- "Đồ cắm trại cho 4 người" → Vector: semantic search
- "Đơn thuê #5 của tôi" → SQL: `WHERE id = 5 AND user_id = ...`

### 2. Rule-based Agent (Legacy)

Dùng regex để detect intent, chạy nhanh hơn nhưng hạn chế.

| Intent | Trigger Keywords | Data Source |
|--------|-----------------|-------------|
| `product_search` | (default) | ChromaDB Vector Search |
| `order_history` | "đơn thuê", "lịch sử" | MySQL Orders |
| `best_sellers` | "hot", "phổ biến" | MySQL Aggregation |
| `check_stock` | "còn hàng", "tồn kho" | MySQL Products |

---

## Data Sync Flow

```mermaid
flowchart LR
    A[(MySQL Products)] -->|"POST /sync"| B[AI Service]
    B --> C[Fetch Outdoor Products]
    C --> D[Create Embeddings]
    D --> E[Store in ChromaDB]
    E --> F[Ready for RAG]
```

---

*Đồ án IS207.Q22 — CampRent — GVHD: ThS. Vũ Minh Sang*
