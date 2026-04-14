import type { Metadata } from "next";
import React from "react";
import { AppHeader } from "@/components/layout/app-header";
import { Footer } from "@/components/layout/footer";
import { ChatWidget } from "@/components/chat/chat-widget";

export const metadata: Metadata = {
  title: {
    template: "%s | CampRent",
    absolute: "CampRent Việt Nam | Thuê đồ cắm trại & outdoor",
  },
};

export default function MainLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <>
      <AppHeader />
      {children}
      <Footer />
      <ChatWidget />
    </>
  );
}
