<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderStatusService;
use Illuminate\Database\Seeder;

class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'customer')->first();
        $admin = User::where('role', 'admin')->first();
        $products = Product::take(6)->get();

        if (! $customer || ! $admin || $products->isEmpty()) {
            return;
        }

        $statuses = [
            OrderStatusService::PENDING,
            OrderStatusService::CONFIRMED,
            OrderStatusService::PACKING,
            OrderStatusService::SHIPPING,
            OrderStatusService::DELIVERED,
            OrderStatusService::COMPLETED,
            OrderStatusService::CANCELLED,
            OrderStatusService::RETURNED,
        ];

        foreach ($statuses as $index => $status) {
            $product = $products[$index % $products->count()];
            $quantity = ($index % 2) + 1;
            $days = ($index % 3) + 2;
            $rentalSubtotal = (float) $product->price * $quantity * $days;
            $depositTotal = (float) $product->deposit_amount * $quantity;
            $insuranceFee = round($rentalSubtotal * 0.05, 2);
            $discountTotal = $index === 5 ? 50000 : 0;
            $totalAmount = $rentalSubtotal + $depositTotal + $insuranceFee - $discountTotal;
            $createdAt = now()->subDays(14 - $index);

            $order = Order::create([
                'user_id' => $customer->id,
                'start_date' => $createdAt->copy()->addDays(2)->toDateString(),
                'end_date' => $createdAt->copy()->addDays(1 + $days)->toDateString(),
                'rental_subtotal' => $rentalSubtotal,
                'deposit_total' => $depositTotal,
                'insurance_fee' => $insuranceFee,
                'shipping_fee' => 0,
                'discount_total' => $discountTotal,
                'total_amount' => $totalAmount,
                'status' => $status,
                'address' => '227 Nguyen Van Cu, Quan 5, TP.HCM',
                'paid_at' => in_array($status, [OrderStatusService::DELIVERED, OrderStatusService::COMPLETED, OrderStatusService::RETURNED], true) ? $createdAt : null,
                'completed_at' => $status === OrderStatusService::COMPLETED ? $createdAt->copy()->addDays(4) : null,
                'cancelled_at' => $status === OrderStatusService::CANCELLED ? $createdAt->copy()->addDay() : null,
                'returned_at' => $status === OrderStatusService::RETURNED ? $createdAt->copy()->addDays(5) : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'start_date' => $order->start_date,
                'end_date' => $order->end_date,
                'price' => $product->price,
                'unit_deposit' => $product->deposit_amount,
                'days' => $days,
                'rental_subtotal' => $rentalSubtotal,
                'discount_amount' => $discountTotal,
                'deposit_total' => $depositTotal,
                'subtotal' => $rentalSubtotal - $discountTotal,
                'total_amount' => $totalAmount,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $index % 2 === 0 ? 'cash' : 'card',
                'amount' => $totalAmount,
                'status' => in_array($status, [OrderStatusService::DELIVERED, OrderStatusService::COMPLETED, OrderStatusService::RETURNED], true) ? 'completed' : 'pending',
                'paid_at' => in_array($status, [OrderStatusService::DELIVERED, OrderStatusService::COMPLETED, OrderStatusService::RETURNED], true) ? $createdAt : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $order->statusHistories()->create([
                'old_status' => null,
                'new_status' => OrderStatusService::PENDING,
                'changed_by' => $customer->id,
                'actor_role' => $customer->role,
                'reason' => 'Demo checkout',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($status !== OrderStatusService::PENDING) {
                $order->statusHistories()->create([
                    'old_status' => OrderStatusService::PENDING,
                    'new_status' => $status,
                    'changed_by' => $admin->id,
                    'actor_role' => $admin->role,
                    'reason' => 'Demo status update',
                    'created_at' => $createdAt->copy()->addHours(6),
                    'updated_at' => $createdAt->copy()->addHours(6),
                ]);
            }
        }
    }
}
