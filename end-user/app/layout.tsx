import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Toaster } from "@/components/ui/sonner";
import { Providers } from "@/components/providers/providers";
import { Analytics } from "@vercel/analytics/next";

const inter = Inter({
  variable: "--font-sans",
  subsets: ["latin", "vietnamese"],
});

export const metadata: Metadata = {
  title: "CampRent - Cho thuê đồ cắm trại & outdoor hàng đầu Việt Nam",
  description:
    "Thuê đồ cắm trại dễ dàng, giá hợp lý. Lều trại, ba lô trekking, bếp dã ngoại — tất cả sẵn sàng cho chuyến phượt của bạn tại CampRent.",
  icons: {
    icon: [
      { rel: "icon", url: "/favicon-32x32.png", sizes: "32x32" },
      { rel: "icon", url: "/favicon-16x16.png", sizes: "16x16" },
    ],
    apple: "/apple-touch-icon.png",
  },
  manifest: "/site.webmanifest",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="vi" suppressHydrationWarning>
      <body
        suppressHydrationWarning
        className={`${inter.variable} antialiased`}
      >
        <Providers>
          <Toaster />
          {children}
          <Analytics />
        </Providers>
      </body>
    </html>
  );
}
