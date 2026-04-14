import type { Metadata } from "next";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export const metadata: Metadata = {
  title: "Câu hỏi thường gặp - CampRent Shop",
  description: "Giải đáp câu hỏi về bán hàng và cho thuê đồ outdoor tại CampRent.",
};

const faqs = [
  { q: "CampRent là gì?", a: "CampRent là nền tảng cho thuê đồ cắm trại và outdoor C2C — nơi bạn có thể đăng cho thuê lều, ba lô, bếp gas, đèn pin và mọi thiết bị outdoor." },
  { q: "Làm sao để đăng sản phẩm?", a: "Bạn chỉ cần đăng ký tài khoản Shop, vào trang quản lý sản phẩm và thêm sản phẩm mới với hình ảnh, mô tả và giá thuê theo ngày." },
  { q: "Tiền đặt cọc hoạt động như thế nào?", a: "Mỗi sản phẩm bạn đăng có thể thiết lập mức đặt cọc riêng. Tiền cọc sẽ được hoàn lại cho khách khi thiết bị trả lại không bị hư hỏng." },
  { q: "Nếu đồ bị hỏng khi trả thì sao?", a: "CampRent có hệ thống Order Evidence — bạn upload ảnh kiểm tra thiết bị khi nhận lại. Chi phí sửa chữa sẽ được trừ từ tiền cọc của khách." },
  { q: "CampRent có giao nhận tận nơi không?", a: "Có! Hỗ trợ giao – nhận tận nơi trong TP.HCM. Các tỉnh thành khác gửi qua chuyển phát nhanh." },
  { q: "CampRent có hoàn tiền không?", a: "CampRent hoàn tiền khi thiết bị không đúng mô tả hoặc gặp lỗi kỹ thuật do nhà cung cấp." },
];

export default function FAQPage() {
  return (
    <main className="min-h-screen py-12">
      <div className="container mx-auto px-4 max-w-4xl">
        <div className="text-center space-y-4 mb-12">
          <h1 className="text-3xl font-bold">Câu hỏi thường gặp</h1>
          <p className="text-muted-foreground">Tìm câu trả lời về sản phẩm và dịch vụ tại CampRent.</p>
        </div>
        <div className="space-y-4">
          {faqs.map((faq, i) => (
            <Card key={i} className="rounded-2xl">
              <CardHeader><CardTitle className="text-lg">{faq.q}</CardTitle></CardHeader>
              <CardContent><p className="text-muted-foreground">{faq.a}</p></CardContent>
            </Card>
          ))}
        </div>
      </div>
    </main>
  );
}
