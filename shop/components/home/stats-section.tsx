import { Card, CardContent } from "@/components/ui/card";
import { TrendingUp, Users, Tent, Award } from "lucide-react";

export function StatsSection() {
  const stats = [
    { icon: Tent, value: "500+", label: "Sản phẩm outdoor", description: "Lều, ba lô, bếp, đèn và nhiều hơn" },
    { icon: Users, value: "3,000+", label: "Chuyến đi thành công", description: "Từ cắm trại đến leo núi" },
    { icon: TrendingUp, value: "99%", label: "Tỷ lệ hài lòng", description: "Thiết bị chất lượng cao" },
    { icon: Award, value: "24/7", label: "Hỗ trợ tư vấn", description: "AI Chatbot & đội ngũ support" },
  ];

  return (
    <section className="py-20 bg-muted/20">
      <div className="container mx-auto px-4">
        <div className="text-center space-y-4 mb-12">
          <h2 className="text-3xl lg:text-4xl font-bold text-balance">Tại sao chọn CampRent?</h2>
          <p className="text-lg text-muted-foreground text-pretty max-w-2xl mx-auto">
            Chúng tôi cam kết mang đến trải nghiệm thuê đồ outdoor tốt nhất — thiết bị chính hãng, giá hợp lý, giao nhận tận nơi.
          </p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {stats.map((stat, index) => (
            <Card key={index} className="text-center rounded-2xl border-0 bg-background/60 backdrop-blur">
              <CardContent className="p-8">
                <div className="space-y-4">
                  <div className="mx-auto w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center">
                    <stat.icon className="h-6 w-6 text-primary" />
                  </div>
                  <div>
                    <div className="text-3xl font-bold text-primary">{stat.value}</div>
                    <div className="font-semibold text-lg">{stat.label}</div>
                    <div className="text-sm text-muted-foreground text-pretty">{stat.description}</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
}
