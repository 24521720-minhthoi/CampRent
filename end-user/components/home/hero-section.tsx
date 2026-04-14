import Link from "next/link";
import { Button } from "@/components/ui/button";
import { ArrowRight, Play } from "lucide-react";

export function HeroSection() {
  return (
    <section className="relative bg-linear-to-br from-background via-background to-muted/20 py-20 lg:py-32">
      <div className="container mx-auto px-4">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          {/* Content */}
          <div className="space-y-8">
            <div className="space-y-4">
              <h1 className="text-4xl lg:text-6xl font-bold text-balance leading-tight">
                Thuê đồ cắm trại
                <span className="text-primary"> dễ dàng</span>, khám phá tự nhiên
              </h1>
              <p className="text-xl text-muted-foreground text-pretty max-w-lg">
                Nền tảng cho thuê đồ outdoor hàng đầu Việt Nam. Từ lều trại,
                ba lô trekking đến bếp dã ngoại — tất cả sẵn sàng cho chuyến phượt của bạn.
              </p>
            </div>

            <div className="flex flex-col sm:flex-row gap-4">
              <Link href="/products">
                <Button size="lg" className="rounded-2xl group">
                  Khám phá ngay
                  <ArrowRight className="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
                </Button>
              </Link>
              <Button
                variant="outline"
                size="lg"
                className="rounded-2xl bg-transparent"
              >
                <Play className="mr-2 h-4 w-4" />
                Xem hướng dẫn
              </Button>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-3 gap-8 pt-8 border-t">
              <div>
                <div className="text-2xl font-bold">500+</div>
                <div className="text-sm text-muted-foreground">Đồ outdoor</div>
              </div>
              <div>
                <div className="text-2xl font-bold">3000+</div>
                <div className="text-sm text-muted-foreground">Chuyến đi</div>
              </div>
              <div>
                <div className="text-2xl font-bold">99%</div>
                <div className="text-sm text-muted-foreground">Hài lòng</div>
              </div>
            </div>
          </div>

          {/* Hero Image */}
          <div className="relative">
            <div className="relative rounded-3xl overflow-hidden bg-linear-to-br from-primary/10 to-primary/5 p-8">
              <img
                src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=800&q=80"
                alt="CampRent - Cho thuê đồ cắm trại"
                className="w-full h-auto rounded-2xl shadow-2xl"
              />

              {/* Floating Cards */}
              <div className="absolute top-4 right-4 bg-background/95 backdrop-blur rounded-2xl p-4 shadow-lg">
                <div className="flex items-center space-x-2">
                  <div className="w-3 h-3 bg-green-500 rounded-full"></div>
                  <span className="text-sm font-medium">Sẵn sàng thuê</span>
                </div>
              </div>

              <div className="absolute bottom-4 left-4 bg-background/95 backdrop-blur rounded-2xl p-4 shadow-lg">
                <div className="text-sm text-muted-foreground">Giá từ</div>
                <div className="text-lg font-bold">25.000đ/ngày</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
