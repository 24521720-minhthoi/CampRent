import type { Metadata } from "next";
import { Card, CardContent } from "@/components/ui/card";
import Link from "next/link";

export const metadata: Metadata = {
  title: "Chính sách bảo mật - CampRent",
  description: "Chính sách bảo mật và quyền riêng tư tại CampRent.",
};

export default function PrivacyPage() {
  return (
    <main className="min-h-screen py-12">
      <div className="container mx-auto px-4 max-w-4xl">
        <h1 className="text-3xl font-bold mb-8 text-center">Chính sách bảo mật</h1>
        
        <Card className="rounded-2xl">
          <CardContent className="p-8 space-y-6 text-muted-foreground">
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">1. Giới thiệu</h2>
              <p>
                Chào mừng bạn đến với CampRent (&quot;chúng tôi&quot;, &quot;của chúng tôi&quot;). Tại
                CampRent, chúng tôi coi trọng quyền riêng tư của bạn và cam kết
                bảo vệ thông tin cá nhân trong quá trình sử dụng dịch vụ cho thuê đồ outdoor.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">2. Thông tin thu thập</h2>
              <p>Chúng tôi thu thập các loại thông tin sau:</p>
              <ul className="list-disc pl-6 mt-2 space-y-1">
                <li>Thông tin đăng ký: họ tên, email, số điện thoại, địa chỉ</li>
                <li>Thông tin giao dịch: đơn hàng thuê, thanh toán, lịch sử thuê</li>
                <li>Thông tin thiết bị: IP, trình duyệt, hệ điều hành</li>
                <li>Tương tác: lịch sử chat với AI chatbot tư vấn sản phẩm</li>
              </ul>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">3. Mục đích sử dụng</h2>
              <ul className="list-disc pl-6 space-y-1">
                <li>Xử lý đơn thuê và giao nhận thiết bị</li>
                <li>Cung cấp hỗ trợ khách hàng và tư vấn AI</li>
                <li>Cải thiện trải nghiệm người dùng và đề xuất sản phẩm phù hợp</li>
                <li>Liên lạc về chương trình khuyến mãi (nếu bạn đồng ý)</li>
              </ul>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">4. Bảo mật dữ liệu</h2>
              <p>
                Thông tin thanh toán được xử lý qua Stripe — đạt chuẩn PCI DSS. Mật khẩu
                được mã hóa bcrypt. Chúng tôi không lưu trữ thông tin thẻ ngân hàng trên hệ thống.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">5. Liên hệ</h2>
              <p>
                Mọi câu hỏi về chính sách bảo mật, vui lòng liên hệ:{" "}
                <Link href="mailto:privacy@camprent.vn">privacy@camprent.vn</Link>
              </p>
            </section>

            <p className="text-sm italic pt-4 border-t">
              Cập nhật lần cuối: Tháng 4, 2026.
            </p>
          </CardContent>
        </Card>
      </div>
    </main>
  );
}
