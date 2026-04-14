<p align="center">
  <a href="https://www.uit.edu.vn/" title="Trường Đại học Công nghệ Thông tin" style="border: 5;">
    <img src="https://i.imgur.com/WmMnSRt.png" alt="Trường Đại học Công nghệ Thông tin | University of Information Technology">
  </a>
</p>

<!-- Title -->
<h1 align="center"><b>IS207.Q22 - CAMPRENT: NỀN TẢNG CHO THUÊ ĐỒ CẮM TRẠI & OUTDOOR</b></h1>

## 📖 Giới thiệu

**CampRent** là nền tảng thương mại điện tử C2C chuyên cho thuê đồ cắm trại, phượt và outdoor. Được xây dựng với kiến trúc Microservices hiện đại, tích hợp AI Chatbot tư vấn sản phẩm thông minh.

**🏫 Môn học:** IS207 - Phát triển ứng dụng Web  
**👨‍🏫 GVHD:** ThS. Vũ Minh Sang  
**📅 Năm học:** 2025 - 2026

**Tính năng nổi bật:**

- **Cho thuê dễ dàng**: Tìm kiếm, thuê và đăng cho thuê đồ outdoor chỉ trong vài bước.
- **Quản lý cửa hàng**: Dashboard quản lý sản phẩm, đơn hàng, doanh thu cho người bán.
- **Trang quản trị**: Giám sát hệ thống, quản lý người dùng và đơn hàng.
- **AI Chatbot**: Tư vấn sản phẩm thông minh dựa trên RAG & Gemini.
- **Thanh toán đa phương thức**: Hỗ trợ tiền mặt, chuyển khoản, thẻ quốc tế (Stripe).
- **Đặt cọc thông minh**: Quản lý tiền cọc và hoàn cọc tự động khi trả đồ.

---

## 🏗 Kiến trúc hệ thống

| Thành phần     | Công nghệ       | Thư mục       | Mô tả                                         |
| :------------- | :--------------- | :------------ | :--------------------------------------------- |
| **Backend**    | Laravel 12 (API) | `/backend`    | REST API, Authentication, Business Logic       |
| **AI Service** | Python (FastAPI) | `/ai-service` | Chatbot RAG (Gemini + ChromaDB)                |
| **End User**   | Next.js 15       | `/end-user`   | Giao diện khách hàng thuê đồ outdoor           |
| **Shop**       | Next.js 15       | `/shop`       | Dashboard quản lý cửa hàng cho người bán       |
| **Admin**      | Next.js 15       | `/admin`      | Trang quản trị hệ thống                        |

---

## 🚀 Hướng dẫn cài đặt

### Yêu cầu

- **Docker Desktop** (Khuyến nghị)
- Hoặc cài đặt thủ công:
  - **PHP** >= 8.2 & Composer
  - **Node.js** >= 20 & npm
  - **Python** >= 3.10
  - **MySQL** hoặc **SQLite**

---

### 🐳 Cài đặt với Docker

#### 1. Backend (Laravel)

```bash
cd backend
cp .env.example .env
docker-compose up -d --build
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
```

_Backend: http://localhost:8000_

#### 2. AI Service

```bash
cd ai-service
cp .env.example .env
# Thêm GOOGLE_API_KEY vào .env
docker-compose up -d --build
```

_AI Service: http://localhost:8001_

---

### 💻 Cài đặt thủ công

#### 1. Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

#### 2. AI Service

```bash
cd ai-service
python3 -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate
pip install -r requirements.txt
uvicorn app.main:app --reload --port 8001
```

#### 3. Frontend

```bash
cd end-user && npm install && npm run dev   # Port 3000
cd shop && npm install && npm run dev       # Port 3001
cd admin && npm install && npm run dev      # Port 3002
```

---

## 📊 Kiểm thử hiệu năng (k6)

```bash
cd k6-testing
docker-compose up
```

---

## 📚 Tài liệu

- **Database Schema**: [docs/db.md](docs/db.md)
- **AI Architecture**: [docs/ai-chatbot-architecture.md](docs/ai-chatbot-architecture.md)

---

## 👥 Thành viên nhóm

| STT | MSSV     | Họ và Tên          | Vai trò                          |
| :-- | :------- | :----------------- | :------------------------------- |
| 1   | 24521720 | Nguyễn Minh Thời   | Nhóm trưởng — PM, Backend, Docs  |
| 2   | 24521469 | Lưu Nhật Quang     | AI Service, Backend Logic        |
| 3   | 24521872 | Bùi Quốc Trung     | Database, Infrastructure         |
| 4   | 24520038 | Chu Huỳnh Khánh An | Frontend, UI/UX, Branding        |

**Lớp:** IS207.Q22  
**GVHD:** ThS. Vũ Minh Sang
