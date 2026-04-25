import type { Metadata } from "next";
import React from "react";
import { AuthHeader } from "@/components/layout/auth-header";
import { Footer } from "@/components/layout/footer";

export const metadata: Metadata = {
  title: {
    template: "%s | CampRent Admin",
    absolute: "Đăng nhập và đăng ký | CampRent Admin",
  },
  description: "Đăng nhập và đăng ký để quản lý hệ thống CampRent",
};

export default function AuthLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <>
      <AuthHeader />
      {children}
      <Footer />
    </>
  );
}
