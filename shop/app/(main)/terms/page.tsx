import type { Metadata } from "next";
import { Card, CardContent } from "@/components/ui/card";

export const metadata: Metadata = {
  title: "Điều khoản sử dụng - CampRent",
  description: "Điều khoản sử dụng dịch vụ cho thuê đồ outdoor tại CampRent.",
};

export default function TermsPage() {
  return (
    <main className="min-h-screen py-12">
      <div className="container mx-auto px-4 max-w-4xl">
        <h1 className="text-3xl font-bold mb-8 text-center">Điều khoản sử dụng</h1>
        <Card className="rounded-2xl">
          <CardContent className="p-8 space-y-6 text-muted-foreground">
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">1. Điều khoản chung</h2>
              <p>Chào mừng bạn đến với CampRent. Khi truy cập website và sử dụng dịch vụ cho thuê đồ cắm trại và outdoor của chúng tôi, bạn đồng ý tuân thủ các điều khoản dưới đây.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">2. Tài khoản người dùng</h2>
              <p>Để thuê hoặc cho thuê đồ trên CampRent, bạn cần đăng ký tài khoản với thông tin chính xác. CampRent có quyền khóa tài khoản nếu phát hiện vi phạm.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">3. Dịch vụ cho thuê</h2>
              <p>CampRent cung cấp dịch vụ cho thuê thiết bị cắm trại, phượt và outdoor bao gồm lều trại, ba lô, bếp dã ngoại, đèn pin, túi ngủ và phụ kiện. Giá thuê tính theo ngày.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">4. Chính sách đặt cọc</h2>
              <p>Mỗi sản phẩm có mức tiền đặt cọc riêng. Tiền cọc hoàn trả khi thiết bị trả đúng hạn và không hư hỏng ngoài hao mòn thông thường.</p>
            </section>
            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">5. Liên hệ</h2>
              <p>Email: <span className="font-medium">support@camprent.vn</span> | Hotline: <span className="font-medium">0901 234 567</span></p>
            </section>
            <p className="text-sm italic pt-4 border-t">Cập nhật: Tháng 4, 2026.</p>
          </CardContent>
        </Card>
      </div>
    </main>
  );
}
