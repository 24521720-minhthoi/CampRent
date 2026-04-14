import type { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
  title: {
    template: "%s | CampRent",
    absolute: "Đăng nhập và đăng ký | CampRent Shop",
  },
};

export default function AuthLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return <>{children}</>;
}
