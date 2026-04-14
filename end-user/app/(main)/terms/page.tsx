import type { Metadata } from "next";
import { Card, CardContent } from "@/components/ui/card";

export const metadata: Metadata = {
  title: "Điều khoản sử dụng - CampRent",
  description: "Điều khoản và điều kiện sử dụng dịch vụ cho thuê đồ outdoor tại CampRent.",
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
              <p>
                Chào mừng bạn đến với CampRent. Khi truy cập website và sử dụng dịch
                vụ cho thuê đồ cắm trại và outdoor của chúng tôi, bạn đồng ý tuân thủ
                các điều khoản và điều kiện dưới đây. Vui lòng đọc kỹ trước khi sử dụng.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">2. Tài khoản người dùng</h2>
              <p>
                Để thuê đồ trên CampRent, bạn phải đăng ký tài khoản với thông tin chính xác.
                Bạn chịu trách nhiệm bảo mật tài khoản và mọi hoạt động dưới tài khoản của mình.
                CampRent có quyền khóa tài khoản nếu phát hiện vi phạm.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">3. Dịch vụ cho thuê</h2>
              <p>
                CampRent cung cấp dịch vụ cho thuê thiết bị cắm trại, phượt và outdoor
                bao gồm nhưng không giới hạn: lều trại, ba lô, bếp dã ngoại, đèn pin,
                túi ngủ, ghế xếp và các phụ kiện outdoor khác. Giá thuê được tính theo ngày.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">4. Chính sách đặt cọc</h2>
              <p>
                Mỗi sản phẩm có mức tiền đặt cọc riêng, được hiển thị rõ trên trang sản phẩm.
                Tiền đặt cọc sẽ được hoàn trả đầy đủ khi thiết bị được trả đúng hạn và không
                bị hư hỏng ngoài mức hao mòn bình thường.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">5. Trách nhiệm khi thuê</h2>
              <p>
                Khách hàng có trách nhiệm bảo quản thiết bị thuê cẩn thận. Mọi hư hỏng, mất mát
                sẽ được đánh giá bằng hệ thống bằng chứng hình ảnh (Order Evidence) và chi phí
                khắc phục sẽ được trừ từ tiền đặt cọc. Đối với hao mòn thông thường do sử dụng
                đúng cách, CampRent sẽ chịu trách nhiệm.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">6. Quyền sở hữu trí tuệ</h2>
              <p>
                Tất cả nội dung bao gồm nhưng không giới hạn: logo, thiết kế, văn bản, hình ảnh,
                phần mềm đều là tài sản của CampRent hoặc các bên cấp phép, được bảo hộ bởi
                luật sở hữu trí tuệ.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">7. Giới hạn trách nhiệm</h2>
              <p>
                CampRent không chịu trách nhiệm cho bất kỳ thiệt hại gián tiếp nào phát sinh
                từ việc sử dụng dịch vụ, bao gồm nhưng không giới hạn các rủi ro trong quá
                trình sử dụng thiết bị outdoor ngoài trời.
              </p>
            </section>

            <section>
              <h2 className="text-xl font-semibold text-foreground mb-3">8. Liên hệ</h2>
              <p>
                Nếu có bất kỳ câu hỏi nào về điều khoản sử dụng, vui lòng liên hệ
                qua email: <span className="font-medium">support@camprent.vn</span> hoặc
                hotline: <span className="font-medium">0901 234 567</span>.
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
