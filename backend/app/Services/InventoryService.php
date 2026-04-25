<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function assertCartItemsAvailable(Collection $cartItems, ?int $excludeOrderId = null): void
    {
        foreach ($cartItems as $item) {
            $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

            if (! $product) {
                throw ValidationException::withMessages(['cart' => 'A product in your cart no longer exists.']);
            }

            if ($product->status !== 'available') {
                throw ValidationException::withMessages(['cart' => "Product {$product->name} is not available."]);
            }

            $available = $this->availableQuantity(
                $product,
                Carbon::parse($item->start_date)->toDateString(),
                Carbon::parse($item->end_date)->toDateString(),
                $excludeOrderId
            );

            if ((int) $item->quantity > $available) {
                throw ValidationException::withMessages([
                    'cart' => "Product {$product->name} has only {$available} unit(s) available for the selected dates.",
                ]);
            }
        }
    }

    public function availableQuantity(Product $product, string $startDate, string $endDate, ?int $excludeOrderId = null): int
    {
        $reserved = InventoryReservation::query()
            ->where('product_id', $product->id)
            ->where('status', InventoryReservation::STATUS_ACTIVE)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->when($excludeOrderId, fn ($query) => $query->where('order_id', '!=', $excludeOrderId))
            ->sum('quantity');

        return max(0, (int) $product->stock - (int) $reserved);
    }

    public function reserveOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            InventoryReservation::create([
                'product_id' => $item->product_id,
                'order_id' => $order->id,
                'order_item_id' => $item->id,
                'quantity' => $item->quantity,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'status' => InventoryReservation::STATUS_ACTIVE,
            ]);
        }
    }
}
