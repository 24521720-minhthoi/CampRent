import React from "react";

export function HeroSection() {
  return (
    <section className="border-b bg-primary/10 dark:bg-primary/5">
      <div className="mx-auto flex max-w-6xl flex-col gap-10 px-4 pb-16 pt-10 md:flex-row md:items-center">
        <div className="md:w-1/2">
          <p className="mb-3 inline-flex items-center rounded-full bg-primary/20 dark:bg-primary/30 px-3 py-1 text-xs font-medium text-primary border border-primary/15 shadow-sm">
            Hon 10.000+ don thue do cam trai moi thang
          </p>

          <h1 className="mb-4 text-4xl font-bold leading-tight tracking-tight text-foreground md:text-5xl">
            Nen tang giup ban{" "}
            <span className="text-primary">cho thue do cam trai</span> de dang
          </h1>

          <p className="mb-6 text-sm text-muted-foreground md:text-base">
            Danh cho cua hang va nguoi ban do outdoor: dang leu, tui ngu, bep
            da ngoai, den trai, ban ghe xep va combo camping chi trong vai buoc.
          </p>

          <div className="mt-6 space-y-4">
            <div className="rounded-2xl border border-primary/25 bg-primary/5 dark:bg-primary/10 px-5 py-4">
              <p className="text-sm font-medium text-primary">
                Ban dang so huu do cam trai nhan roi?
              </p>
              <p className="mt-1 text-sm text-muted-foreground">
                Dang leu, balo trekking, bep gas mini hoac bo noi camping de
                tiep can khach thue nhanh hon va toi uu cong suat cho thue.
              </p>
            </div>

            <p className="text-xs text-muted-foreground">
              Ho tro tao bao gia - Quan ly lich thue - Theo doi trang thai don ro rang.
            </p>
          </div>
        </div>

        <div className="md:w-1/2">
          <div className="relative overflow-hidden rounded-3xl bg-card shadow-md">
            <div
              className="h-64 bg-cover bg-center md:h-72"
              style={{
                backgroundImage:
                  "url('https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1400&q=80')",
              }}
            />

            <div className="space-y-2 px-5 py-4">
              <p className="text-xs font-semibold text-primary">
                Danh cho nha cung cap outdoor
              </p>
              <p className="text-sm font-semibold text-foreground">
                Nhan don thue leu, bep da ngoai va combo camping
              </p>
              <p className="text-xs text-muted-foreground">
                Lich ro rang - Bao gia nhanh - Theo doi trang thai don thue
              </p>
            </div>

            <div className="absolute right-4 top-4 rounded-full bg-background/80 backdrop-blur-sm px-3 py-1 text-xs font-medium text-primary shadow">
              Duoc nhieu cua hang outdoor tin dung
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

export default HeroSection;
