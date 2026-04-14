import type { Metadata } from "next";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export const metadata: Metadata = {
  title: "Câu hỏi thường gặp - CampRent",
  description: "Giải đáp các câu hỏi về dịch vụ cho thuê đồ cắm trại và outdoor tại CampRent.",
};

const faqs = [
  {
    q: "CampRent là gì?",
    a: "CampRent là nền tảng cho thuê đồ cắm trại và outdoor C2C — nơi bạn có thể thuê lều, ba lô, bếp gas, đèn pin và mọi thiết bị cần thiết cho chuyến phượt, leo núi, cắm trại.",
  },
  {
    q: "Làm sao để thuê đồ trên CampRent?",
    a: "Bạn chỉ cần đăng ký tài khoản, chọn sản phẩm cần thuê, chọn ngày bắt đầu - kết thúc, thanh toán và nhận đồ. Đơn giản chỉ trong 3 bước!",
  },
  {
    q: "Tiền đặt cọc hoạt động như thế nào?",
    a: "Mỗi sản phẩm có mức đặt cọc riêng (hiển thị rõ trên trang sản phẩm). Tiền cọc sẽ được hoàn lại đầy đủ khi bạn trả đồ đúng hạn và thiết bị không bị hư hỏng.",
  },
  {
    q: "Nếu đồ thuê bị hỏng thì sao?",
    a: "Trong trường hợp thiết bị bị hư hỏng, chi phí sửa chữa sẽ được trừ vào tiền đặt cọc. Chúng tôi có chính sách đánh giá hư hỏng công bằng với hình ảnh bằng chứng.",
  },
  {
    q: "CampRent có giao nhận tận nơi không?",
    a: "Có! CampRent hỗ trợ giao – nhận tận nơi trong nội thành TP.HCM. Đối với các tỉnh thành khác, chúng tôi gửi qua dịch vụ chuyển phát nhanh.",
  },
  {
    q: "Tôi có thể thuê đồ bao lâu?",
    a: "Thời gian thuê tối thiểu là 1 ngày, tối đa 30 ngày. Nếu cần thuê dài hơn, bạn có thể liên hệ trực tiếp với shop hoặc hỗ trợ khách hàng.",
  },
  {
    q: "Làm sao để trở thành người bán trên CampRent?",
    a: "Bạn đăng ký tài khoản shop, liệt kê sản phẩm cần cho thuê với hình ảnh và mô tả chi tiết. Sau khi được duyệt, sản phẩm sẽ hiển thị trên CampRent.",
  },
  {
    q: "CampRent có hoàn tiền không?",
    a: "CampRent hỗ trợ hoàn tiền trong trường hợp thiết bị không đúng mô tả, bị lỗi kỹ thuật, hoặc đơn hàng bị hủy trước khi giao.",
  },
];

export default function FAQPage() {
  return (
    <main className="min-h-screen py-12">
      <div className="container mx-auto px-4 max-w-4xl">
        <div className="text-center space-y-4 mb-12">
          <h1 className="text-3xl font-bold">Câu hỏi thường gặp</h1>
          <p className="text-muted-foreground">
            Tìm câu trả lời cho các câu hỏi phổ biến về dịch vụ cho thuê đồ outdoor tại CampRent.
          </p>
        </div>

        <div className="space-y-4">
          {faqs.map((faq, index) => (
            <Card key={index} className="rounded-2xl">
              <CardHeader>
                <CardTitle className="text-lg">{faq.q}</CardTitle>
              </CardHeader>
              <CardContent>
                <p className="text-muted-foreground">{faq.a}</p>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </main>
  );
}
