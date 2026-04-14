import type { Metadata } from "next";
import React from "react";

export const metadata: Metadata = {
  title: {
    template: "%s | CampRent Shop",
    absolute: "CampRent Shop | Quản lý cửa hàng outdoor",
  },
};

export default function MainLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return <>{children}</>;
}
