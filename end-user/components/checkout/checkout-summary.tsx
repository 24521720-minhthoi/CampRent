"use client";

import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { PaymentMethod } from "@/lib/enum";
import type { CartItem, PricingSummary } from "@/lib/types";
import { formatCurrency, formatDate, getPaymentMethodLabel } from "@/lib/utils";
import { Calendar, Loader2 } from "lucide-react";

interface CheckoutSummaryProps {
  items: CartItem[];
  totalAmount: number;
  summary?: PricingSummary;
  paymentMethod: PaymentMethod;
  onSubmit: () => void;
  isProcessing: boolean;
}

export function CheckoutSummary({
  items,
  totalAmount,
  summary,
  paymentMethod,
  onSubmit,
  isProcessing,
}: CheckoutSummaryProps) {
  const pricing = summary ?? {
    rental_subtotal: totalAmount,
    deposit_total: 0,
    insurance_fee: 0,
    shipping_fee: 0,
    discount_total: 0,
    total_amount: totalAmount,
    discounts: [],
  };

  return (
    <Card className="rounded-2xl border-0 bg-background/60 backdrop-blur sticky top-8">
      <CardHeader>
        <CardTitle>Order summary</CardTitle>
      </CardHeader>
      <CardContent className="space-y-6">
        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.id} className="flex gap-3">
              <div className="w-16 h-16 rounded-xl overflow-hidden bg-linear-to-br from-muted/50 to-muted/20 flex-shrink-0">
                <img
                  src={item.product.image_url || "/file.svg"}
                  alt={item.product.name}
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="flex-1 space-y-1">
                <h4 className="font-medium text-sm line-clamp-2">
                  {item.product.name}
                </h4>
                <div className="flex items-center space-x-1 text-xs text-muted-foreground">
                  <Calendar className="h-3 w-3" />
                  <span>
                    {formatDate(item.start_date)} - {formatDate(item.end_date)}
                  </span>
                </div>
                <div className="flex items-center justify-between text-sm">
                  <span className="text-muted-foreground">
                    {item.quantity} x {item.days} days
                  </span>
                  <span className="font-medium">
                    {formatCurrency(item.total_price)}
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>

        <Separator />

        <div className="space-y-3">
          <div className="flex justify-between text-sm">
            <span>Rental subtotal</span>
            <span>{formatCurrency(pricing.rental_subtotal)}</span>
          </div>
          <div className="flex justify-between text-sm">
            <span>Deposit</span>
            <span>{formatCurrency(pricing.deposit_total)}</span>
          </div>
          {pricing.discount_total > 0 && (
            <div className="flex justify-between text-sm text-green-600">
              <span>Discount</span>
              <span>-{formatCurrency(pricing.discount_total)}</span>
            </div>
          )}
          {pricing.discounts?.map((discount) => (
            <div
              key={`${discount.promotion_id}-${discount.level}`}
              className="flex justify-between text-xs text-muted-foreground"
            >
              <span>{discount.name}</span>
              <span>-{formatCurrency(discount.amount)}</span>
            </div>
          ))}
          <div className="flex justify-between text-sm">
            <span>Shipping</span>
            <span>
              {pricing.shipping_fee > 0
                ? formatCurrency(pricing.shipping_fee)
                : "Free"}
            </span>
          </div>
          <div className="flex justify-between text-sm">
            <span>Insurance fee</span>
            <span>{formatCurrency(pricing.insurance_fee)}</span>
          </div>
          <Separator />
          <div className="flex justify-between font-semibold text-lg">
            <span>Total</span>
            <span className="text-primary">
              {formatCurrency(pricing.total_amount)}
            </span>
          </div>
        </div>

        <Separator />

        <div className="space-y-2">
          <div className="text-sm font-medium">Payment method</div>
          <div className="text-sm text-muted-foreground">
            {getPaymentMethodLabel(paymentMethod)}
          </div>
        </div>

        <Button
          size="lg"
          className="w-full rounded-2xl"
          onClick={onSubmit}
          disabled={isProcessing}
        >
          {isProcessing ? (
            <>
              <Loader2 className="mr-2 h-4 w-4 animate-spin" />
              Processing...
            </>
          ) : (
            "Confirm rental"
          )}
        </Button>

        <p className="text-xs text-muted-foreground text-center">
          By confirming, you agree to the rental terms and return policy.
        </p>
      </CardContent>
    </Card>
  );
}
