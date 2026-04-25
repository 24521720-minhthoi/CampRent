<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PricingService
{
    public const INSURANCE_RATE = 0.05;

    public function __construct(private PromotionService $promotionService)
    {
    }

    public function calculateCart(Collection $cartItems, ?User $user = null, ?string $promotionCode = null): array
    {
        $items = collect();
        $discounts = collect();
        $rentalSubtotal = 0.0;
        $depositTotal = 0.0;
        $itemDiscountTotal = 0.0;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if (! $product) {
                throw ValidationException::withMessages(['cart' => 'A product in your cart no longer exists.']);
            }

            if ($product->status !== 'available') {
                throw ValidationException::withMessages(['cart' => "Product {$product->name} is not available."]);
            }

            $startDate = Carbon::parse($cartItem->start_date)->startOfDay();
            $endDate = Carbon::parse($cartItem->end_date)->startOfDay();

            if ($endDate->lt($startDate)) {
                throw ValidationException::withMessages(['cart' => "Product {$product->name} has an invalid rental period."]);
            }

            $days = (int) $startDate->diffInDays($endDate) + 1;
            $unitPrice = (float) $product->price;
            $unitDeposit = (float) ($product->deposit_amount ?? 0);
            $quantity = (int) $cartItem->quantity;
            $baseRental = round($unitPrice * $days * $quantity, 2);
            $deposit = round($unitDeposit * $quantity, 2);

            $itemDiscount = $this->promotionService->applyBestAutomaticPromotion(
                $product,
                $user,
                $baseRental,
                $quantity,
                $days
            );

            $discountAmount = $itemDiscount['amount'] ?? 0.0;

            if ($itemDiscount) {
                $discounts->push($itemDiscount);
            }

            $pricedItem = [
                'cart_item_id' => $cartItem->id,
                'product' => $product,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days' => $days,
                'unit_price' => $unitPrice,
                'unit_deposit' => $unitDeposit,
                'rental_subtotal' => $baseRental,
                'discount_amount' => round($discountAmount, 2),
                'deposit_total' => $deposit,
                'subtotal' => round(max(0, $baseRental - $discountAmount), 2),
                'total_amount' => round(max(0, $baseRental - $discountAmount) + $deposit, 2),
                'promotion_id' => $itemDiscount['promotion_id'] ?? null,
            ];

            $items->push($pricedItem);
            $rentalSubtotal += $baseRental;
            $depositTotal += $deposit;
            $itemDiscountTotal += $discountAmount;
        }

        $orderDiscount = null;
        $discountableSubtotal = max(0, $rentalSubtotal - $itemDiscountTotal);

        if ($promotionCode) {
            $orderDiscount = $this->promotionService->applyVoucher($promotionCode, $user, $items, $discountableSubtotal);
            $discounts->push($orderDiscount);
        }

        $discountTotal = round($itemDiscountTotal + ($orderDiscount['amount'] ?? 0), 2);
        $rentalAfterDiscount = round(max(0, $rentalSubtotal - $discountTotal), 2);
        $insuranceFee = round($rentalAfterDiscount * self::INSURANCE_RATE, 2);
        $shippingFee = 0.0;
        $totalAmount = round($rentalAfterDiscount + $depositTotal + $insuranceFee + $shippingFee, 2);

        return [
            'items' => $items->map(function (array $item) {
                unset($item['product']);
                return $item;
            })->values()->all(),
            'rental_subtotal' => round($rentalSubtotal, 2),
            'deposit_total' => round($depositTotal, 2),
            'insurance_fee' => $insuranceFee,
            'shipping_fee' => $shippingFee,
            'discount_total' => $discountTotal,
            'total_amount' => $totalAmount,
            'discounts' => $this->aggregateDiscounts($discounts)->values()->all(),
        ];
    }

    public function syncCartPrices(Collection $cartItems, array $pricing): void
    {
        $byCartItem = collect($pricing['items'])->keyBy('cart_item_id');

        foreach ($cartItems as $cartItem) {
            $priced = $byCartItem->get($cartItem->id);

            if (! $priced) {
                continue;
            }

            $cartItem->forceFill([
                'days' => $priced['days'],
                'total_price' => $priced['total_amount'],
            ])->save();
        }
    }

    private function aggregateDiscounts(Collection $discounts): Collection
    {
        return $discounts
            ->groupBy(fn (array $discount) => ($discount['promotion_id'] ?? 'manual') . ':' . ($discount['level'] ?? 'order'))
            ->map(function (Collection $group) {
                $first = $group->first();
                $first['amount'] = round($group->sum('amount'), 2);

                return $first;
            });
    }
}
