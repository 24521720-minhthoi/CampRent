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
              <p>Chào mừng bạn đến với CampRent. Chúng tôi coi trọng quyền riêng tư của bạn và cam kết bảo vệ thông tin cá nhân trong quá trình sử dụng dịch vụ cho thuê đồ outdoor.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">2. Thông tin thu thập</h2>
              <ul className="list-disc pl-6 mt-2 space-y-1">
                <li>Thông tin đăng ký: họ tên, email, số điện thoại, địa chỉ</li>
                <li>Thông tin giao dịch: đơn thuê, thanh toán, lịch sử thuê</li>
                <li>Tương tác: lịch sử chat với AI chatbot tư vấn sản phẩm</li>
              </ul>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">3. Bảo mật dữ liệu</h2>
              <p>Thông tin thanh toán qua Stripe — PCI DSS. Mật khẩu mã hóa bcrypt. Không lưu thông tin thẻ ngân hàng.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">4. Liên hệ</h2>
              <p>Câu hỏi bảo mật: <Link href="mailto:privacy@camprent.vn">privacy@camprent.vn</Link></p>
            </section>
            <p className="text-sm italic pt-4 border-t">Cập nhật: Tháng 4, 2026.</p>
          </CardContent>
        </Card>
      </div>
    </main>
  );
}
