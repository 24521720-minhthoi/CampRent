import type { Metadata } from "next";
import { OrdersClient } from "@/components/orders/orders-client";

export const metadata: Metadata = {
  title: "Orders | CampRent Shop",
};

export default function OrdersPage() {
  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-balance">Orders</h1>
        <p className="mt-2 text-muted-foreground">
          Track orders that contain your shop products.
        </p>
      </div>
      <OrdersClient />
    </div>
  );
}
