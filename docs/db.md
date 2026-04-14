# Tài liệu CSDL — CampRent

Ngày cập nhật: 2026-04-08\
Dự án: IS207.Q22 – CampRent: Nền tảng cho thuê đồ cắm trại & outdoor (Laravel + Next.js)\
GVHD: ThS. Vũ Minh Sang

## Tổng quan cơ sở dữ liệu

| Bảng                   | Mục đích                 | Ghi chú                                    |
| ---------------------- | ------------------------ | ------------------------------------------ |
| users                  | Lưu thông tin người dùng | Role, địa chỉ, thông tin Stripe            |
| password_reset_tokens  | Token đặt lại mật khẩu   | Hỗ trợ quên mật khẩu                       |
| sessions               | Phiên đăng nhập          | Theo dõi hoạt động phiên                   |
| personal_access_tokens | Token truy cập cá nhân   | Dùng cho API/Auth (Sanctum)                |
| categories             | Danh mục đồ outdoor      | Lều, ba lô, nấu nướng, ánh sáng...        |
| products               | Sản phẩm outdoor         | FK category_id, shop_id; trạng thái enum   |
| comments               | Bình luận sản phẩm       | Hỗ trợ cây bình luận + edited flag         |
| orders                 | Đơn thuê                 | Trạng thái enum, ngày thuê, địa chỉ       |
| order_items            | Mục trong đơn thuê       | Liên kết sản phẩm và đơn hàng              |
| payments               | Thanh toán               | Phương thức + trạng thái                   |
| carts                  | Giỏ hàng                 | Gắn với user (nullable)                    |
| cart_items             | Mục trong giỏ            | Số lượng + thời gian thuê + tổng tiền      |
| order_evidence         | Bằng chứng trả đồ       | Hình ảnh kiểm tra thiết bị khi trả         |
| product_price_history  | Lịch sử thay đổi giá    | Theo dõi ai đổi giá, lý do                 |
| conversations          | Hội thoại AI             | Chat với AI chatbot                         |
| messages               | Tin nhắn chat            | Nội dung từng message trong conversation    |

## Chi tiết schema chính

### products (đã cập nhật)

| Cột            | Kiểu                                            | Null | Default   | Ghi chú                              |
| -------------- | ------------------------------------------------ | ---- | --------- | ------------------------------------- |
| id             | bigIncrements                                    | No   |           | PK                                    |
| name           | string                                           | No   |           | Tên sản phẩm outdoor                  |
| slug           | string                                           | No   |           | unique                                |
| description    | text                                             | Yes  |           | Mô tả chi tiết                        |
| price          | decimal(10,2)                                    | No   |           | Giá thuê / ngày (VNĐ)                |
| deposit_amount | decimal(10,2)                                    | No   | 0         | Tiền đặt cọc (VNĐ)                   |
| stock          | integer                                          | No   | 0         | Số lượng còn                          |
| image_url      | string                                           | Yes  |           | Ảnh chính                             |
| images         | json                                             | Yes  |           | Mảng ảnh phụ                          |
| status         | enum('available','rented','maintenance')         | No   | available | Trạng thái sản phẩm                  |
| category_id    | foreignId                                        | No   |           | FK → categories(id), cascadeOnDelete  |
| shop_id        | foreignId                                        | No   |           | FK → users(id), cascadeOnDelete       |

### orders

| Cột          | Kiểu                                                                                  | Null | Default | Ghi chú                           |
| ------------ | -------------------------------------------------------------------------------------- | ---- | ------- | ---------------------------------- |
| id           | bigIncrements                                                                          | No   |         | PK                                 |
| user_id      | foreignId                                                                              | No   |         | FK → users(id)                    |
| start_date   | date                                                                                   | No   |         | Ngày bắt đầu thuê                 |
| end_date     | date                                                                                   | No   |         | Ngày trả đồ                       |
| total_amount | decimal(10,2)                                                                          | No   |         | Tổng tiền thuê                    |
| status       | enum('pending','confirmed','processing','shipped','delivered','returned','cancelled')  | No   | pending | Trạng thái đơn thuê               |
| address      | string                                                                                 | Yes  |         | Địa chỉ giao đồ                  |

## Quan hệ chính (ER tóm tắt)

| Từ bảng       | Đến bảng           | Kiểu quan hệ | Ghi chú                |
| ------------- | ------------------ | ------------- | ----------------------- |
| users         | orders             | 1–N           | user_id                 |
| users         | carts              | 1–N           | user_id (nullable)      |
| users         | products           | 1–N           | shop_id (người bán)     |
| users         | comments           | 1–N           | user_id                 |
| categories    | products           | 1–N           | category_id             |
| products      | comments           | 1–N           | product_id              |
| orders        | order_items        | 1–N           | order_id                |
| products      | order_items        | 1–N           | product_id              |
| carts         | cart_items         | 1–N           | cart_id                 |
| products      | cart_items         | 1–N           | product_id              |
| orders        | payments           | 1–N           | order_id                |
| orders        | order_evidence     | 1–N           | order_id                |
| products      | product_price_hist.| 1–N           | product_id              |
| users         | conversations      | 1–N           | user_id                 |
| conversations | messages           | 1–N           | conversation_id         |
| comments      | comments           | 1–N (cây)     | parent_id + left/right  |

---

*Đồ án IS207.Q22 — CampRent — GVHD: ThS. Vũ Minh Sang*
