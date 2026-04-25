import { clsx, type ClassValue } from "clsx";
import { CheckCircle, Clock, Package, RotateCcw, Truck, XCircle } from "lucide-react";
import { twMerge } from "tailwind-merge";
import { OrderStatus, PaymentMethod, PaymentStatus } from "./enum";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatCurrency(amount: string | number): string {
  return new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(Number(amount));
}

export function formatDate(date: string): string {
  return new Intl.DateTimeFormat("vi-VN", {
    year: "numeric",
    month: "long",
    day: "numeric",
  }).format(new Date(date));
}

export function calculateDays(startDate: string, endDate: string): number {
  const start = new Date(startDate);
  const end = new Date(endDate);
  const diffTime = end.getTime() - start.getTime();
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
  return Math.max(1, diffDays + 1);
}

export function getPaymentMethodLabel(method: PaymentMethod): string {
  switch (method) {
    case PaymentMethod.CASH:
      return "Cash on delivery";
    case PaymentMethod.BANK_TRANSFER:
      return "Bank transfer";
    case PaymentMethod.CARD:
      return "Card";
    default:
      return "Cash";
  }
}

export function getInitials(name: string): string {
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

export const statusConfig: Record<
  OrderStatus,
  {
    label: string;
    variant: "default" | "secondary" | "destructive" | "outline";
    icon: any;
  }
> = {
  pending: { label: "Pending", variant: "secondary", icon: Clock },
  confirmed: { label: "Confirmed", variant: "default", icon: CheckCircle },
  packing: { label: "Packing", variant: "default", icon: Package },
  shipping: { label: "Shipping", variant: "default", icon: Truck },
  delivered: { label: "Delivered", variant: "default", icon: CheckCircle },
  completed: { label: "Completed", variant: "default", icon: CheckCircle },
  cancelled: { label: "Cancelled", variant: "destructive", icon: XCircle },
  returned: { label: "Returned", variant: "outline", icon: RotateCcw },
  refunded: { label: "Refunded", variant: "secondary", icon: RotateCcw },
};

export const paymentStatusConfig: Record<
  PaymentStatus,
  {
    label: string;
    variant: "default" | "secondary" | "destructive" | "outline";
  }
> = {
  pending: { label: "Pending", variant: "secondary" },
  completed: { label: "Completed", variant: "default" },
  failed: { label: "Failed", variant: "destructive" },
};
