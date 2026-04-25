<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionUsage;
use App\Models\User;
use App\Services\CheckoutService;
use App\Services\DashboardService;
use App\Services\OrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RentalCommerceBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_status_transitions_are_enforced_and_logged(): void
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $customer->id,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-03',
            'rental_subtotal' => 300,
            'deposit_total' => 0,
            'insurance_fee' => 15,
            'shipping_fee' => 0,
            'discount_total' => 0,
            'total_amount' => 315,
            'status' => OrderStatusService::PENDING,
        ]);

        $service = app(OrderStatusService::class);
        $service->recordInitialStatus($order, $customer, 'created');
        $service->transition($order, OrderStatusService::CONFIRMED, $admin, 'approved');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatusService::CONFIRMED,
        ]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'old_status' => OrderStatusService::PENDING,
            'new_status' => OrderStatusService::CONFIRMED,
            'changed_by' => $admin->id,
            'reason' => 'approved',
        ]);

        $this->expectException(ValidationException::class);
        $service->transition($order->fresh(), OrderStatusService::DELIVERED, $admin, 'invalid jump');
    }

    public function test_checkout_totals_snapshot_dates_deposit_insurance_and_reservation_are_consistent(): void
    {
        $user = User::factory()->create();
        $product = $this->product(stock: 3, price: 100, deposit: 250);
        $this->cartItem($user, $product, quantity: 2, start: '2026-06-01', end: '2026-06-03');

        $result = app(CheckoutService::class)->createOrderFromCart($user, '123 Test St', 'cash');
        $order = $result['order']->fresh(['items', 'payment']);

        $this->assertSame('pending', $order->status);
        $this->assertSame('600.00', $order->rental_subtotal);
        $this->assertSame('500.00', $order->deposit_total);
        $this->assertSame('30.00', $order->insurance_fee);
        $this->assertSame('1130.00', $order->total_amount);
        $this->assertSame('2026-06-01', $order->items->first()->start_date->toDateString());
        $this->assertSame('2026-06-03', $order->items->first()->end_date->toDateString());
        $this->assertSame('1130.00', $order->payment->amount);
        $this->assertDatabaseHas('inventory_reservations', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'status' => InventoryReservation::STATUS_ACTIVE,
        ]);
    }

    public function test_promotion_application_and_per_user_anti_cheat_limit(): void
    {
        $user = User::factory()->create();
        $product = $this->product(stock: 5, price: 100, deposit: 0);
        $promotion = Promotion::create([
            'name' => 'Save 10',
            'code' => 'SAVE10',
            'type' => 'percent',
            'value' => 10,
            'scope' => 'all',
            'per_user_limit' => 1,
            'status' => 'active',
            'is_active' => true,
        ]);

        $this->cartItem($user, $product, quantity: 2, start: '2026-07-01', end: '2026-07-03');
        $first = app(CheckoutService::class)->createOrderFromCart($user, '123 Test St', 'cash', 'SAVE10');

        $this->assertSame(60.0, $first['pricing']['discount_total']);
        $this->assertDatabaseHas('promotion_usages', [
            'promotion_id' => $promotion->id,
            'user_id' => $user->id,
        ]);

        CartItem::query()->delete();
        $this->cartItem($user, $product, quantity: 1, start: '2026-08-01', end: '2026-08-02');

        $this->expectException(ValidationException::class);
        app(CheckoutService::class)->createOrderFromCart($user, '123 Test St', 'cash', 'SAVE10');
    }

    public function test_rental_overlap_blocks_double_booking(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $product = $this->product(stock: 1, price: 100, deposit: 0);

        $this->cartItem($firstUser, $product, quantity: 1, start: '2026-09-01', end: '2026-09-05');
        app(CheckoutService::class)->createOrderFromCart($firstUser, '123 Test St', 'cash');

        $this->cartItem($secondUser, $product, quantity: 1, start: '2026-09-03', end: '2026-09-04');

        $this->expectException(ValidationException::class);
        app(CheckoutService::class)->createOrderFromCart($secondUser, '456 Test St', 'cash');
    }

    public function test_dashboard_aggregates_real_paid_orders_customers_and_best_sellers(): void
    {
        $user = User::factory()->create(['name' => 'Real Customer']);
        $product = $this->product(stock: 5, price: 100, deposit: 0);
        $this->cartItem($user, $product, quantity: 1, start: '2026-10-01', end: '2026-10-01');
        $result = app(CheckoutService::class)->createOrderFromCart($user, '123 Test St', 'cash');
        $order = $result['order'];
        $order->update(['status' => OrderStatusService::COMPLETED]);
        $order->payment->update(['status' => 'completed', 'paid_at' => now()]);

        $data = app(DashboardService::class)->data(Request::create('/admin/dashboard', 'GET', [
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'group_by' => 'day',
        ]));

        $this->assertSame('105', (string) (int) $data['stats']['totalRevenue']);
        $this->assertSame('Real Customer', $data['topCustomersBySpend'][0]['name']);
        $this->assertSame($product->name, $data['bestSellers'][0]['name']);
    }

    private function product(int $stock, int $price, int $deposit): Product
    {
        $shop = User::factory()->create(['role' => 'shop']);
        $category = Category::firstOrCreate(
            ['slug' => 'tents'],
            ['name' => 'Tents', 'description' => 'Camping tents']
        );

        return Product::create([
            'name' => 'Test Tent',
            'slug' => 'test-tent-' . uniqid(),
            'description' => 'A reliable rental tent.',
            'price' => $price,
            'deposit_amount' => $deposit,
            'stock' => $stock,
            'status' => 'available',
            'category_id' => $category->id,
            'shop_id' => $shop->id,
        ]);
    }

    private function cartItem(User $user, Product $product, int $quantity, string $start, string $end): CartItem
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'start_date' => $start,
            'end_date' => $end,
            'days' => 1,
            'total_price' => 0,
        ]);
    }
}
