<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private PricingService $pricingService,
        private InventoryService $inventoryService,
        private PromotionService $promotionService,
        private OrderStatusService $statusService,
    ) {
    }

    public function createOrderFromCart(User $user, string $address, string $paymentMethod, ?string $promotionCode = null): array
    {
        return DB::transaction(function () use ($user, $address, $paymentMethod, $promotionCode) {
            $cartItems = CartItem::whereHas('cart', fn ($query) => $query->where('user_id', $user->id))
                ->with('product.category')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Cart is empty.']);
            }

            $pricing = $this->pricingService->calculateCart($cartItems, $user, $promotionCode);
            $this->pricingService->syncCartPrices($cartItems, $pricing);
            $this->inventoryService->assertCartItemsAvailable($cartItems);

            $order = Order::create([
                'user_id' => $user->id,
                'start_date' => $cartItems->min('start_date'),
                'end_date' => $cartItems->max('end_date'),
                'rental_subtotal' => $pricing['rental_subtotal'],
                'deposit_total' => $pricing['deposit_total'],
                'insurance_fee' => $pricing['insurance_fee'],
                'shipping_fee' => $pricing['shipping_fee'],
                'discount_total' => $pricing['discount_total'],
                'total_amount' => $pricing['total_amount'],
                'pricing_snapshot' => $pricing,
                'status' => OrderStatusService::PENDING,
                'address' => $address,
            ]);

            $pricedItems = collect($pricing['items'])->keyBy('cart_item_id');

            foreach ($cartItems as $cartItem) {
                $priced = $pricedItems->get($cartItem->id);

                if (! $priced) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $priced['quantity'],
                    'start_date' => $priced['start_date'],
                    'end_date' => $priced['end_date'],
                    'price' => $priced['unit_price'],
                    'unit_deposit' => $priced['unit_deposit'],
                    'days' => $priced['days'],
                    'rental_subtotal' => $priced['rental_subtotal'],
                    'discount_amount' => $priced['discount_amount'],
                    'deposit_total' => $priced['deposit_total'],
                    'subtotal' => $priced['subtotal'],
                    'total_amount' => $priced['total_amount'],
                    'promotion_id' => $priced['promotion_id'],
                    'pricing_snapshot' => $priced,
                ]);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $pricing['total_amount'],
                'status' => 'pending',
            ]);

            $order->load('items');
            $this->inventoryService->reserveOrder($order);
            $this->promotionService->recordUsages($order, $pricing, $user);
            $this->statusService->recordInitialStatus($order, $user, 'checkout_created');

            return [
                'order' => $order->fresh(['user', 'items.product', 'payment', 'statusHistories.actor', 'discounts']),
                'pricing' => $pricing,
            ];
        });
    }
}
