import { Users, Tent, Mountain, Heart } from "lucide-react";

export function HeroSection() {
  return (
    <section className="relative bg-linear-to-br from-primary/5 via-background to-muted/20 py-20 lg:py-28">
      <div className="container mx-auto px-4 text-center space-y-6">
        <h1 className="text-4xl lg:text-5xl font-bold text-balance">
          Về <span className="text-primary">CampRent</span>
        </h1>
        <p className="text-xl text-muted-foreground max-w-3xl mx-auto text-pretty">
          CampRent được thành lập bởi nhóm sinh viên UIT với niềm đam mê outdoor
          và mong muốn giúp mọi người tiếp cận thiết bị cắm trại chất lượng cao
          mà không cần đầu tư hàng chục triệu đồng.
        </p>
      </div>
    </section>
  );
}
