<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        private PricingService $pricingService,
        private InventoryService $inventoryService,
    ) {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $cart = Cart::with('items.product.category')
            ->where('user_id', $user->id)
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json([
                'data' => [],
                'summary' => $this->emptySummary(),
                'total_items' => 0,
                'total_price' => 0,
            ]);
        }

        $pricing = $this->pricingService->calculateCart(
            $cart->items,
            $user,
            $request->query('promotion_code')
        );
        $this->pricingService->syncCartPrices($cart->items, $pricing);

        $cart->load('items.product.category');

        return response()->json([
            'data' => $cart->items,
            'summary' => $pricing,
            'total_items' => $cart->items->sum('quantity'),
            'total_price' => $pricing['total_amount'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::findOrFail($validated['product_id']);
        $days = $this->calculateRentalDays($validated['start_date'], $validated['end_date']);

        $existing = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->whereDate('start_date', $validated['start_date'])
            ->whereDate('end_date', $validated['end_date'])
            ->first();

        $requestedQuantity = (int) $validated['quantity'] + (int) ($existing?->quantity ?? 0);
        $this->assertProductCanBeAdded($product, $validated['start_date'], $validated['end_date'], $requestedQuantity);

        if ($existing) {
            $existing->update(['quantity' => $requestedQuantity]);
            $cartItem = $existing;
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'days' => $days,
                'total_price' => 0,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);
        }

        $this->refreshUserCartPricing($cart, $user);

        return response()->json([
            'message' => 'Product added to cart successfully',
            'data' => $cartItem->fresh('product.category'),
        ], 201);
    }

    public function update(Request $request, $itemId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user = Auth::user();
        $cartItem = CartItem::with(['cart', 'product'])->findOrFail($itemId);

        if ($cartItem->cart->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized access to this cart item'], 403);
        }

        $this->assertProductCanBeAdded($cartItem->product, $validated['start_date'], $validated['end_date'], (int) $validated['quantity']);

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'days' => $this->calculateRentalDays($validated['start_date'], $validated['end_date']),
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        $this->refreshUserCartPricing($cartItem->cart, $user);

        return response()->json([
            'message' => 'Cart item updated successfully',
            'data' => $cartItem->fresh('product.category'),
        ]);
    }

    public function destroy($itemId)
    {
        $user = Auth::user();
        $cartItem = CartItem::with('cart')->findOrFail($itemId);

        if ($cartItem->cart->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized access to this cart item'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed']);
    }

    public function clear()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }

    private function assertProductCanBeAdded(Product $product, string $startDate, string $endDate, int $quantity): void
    {
        if ($product->status !== 'available') {
            throw ValidationException::withMessages(['product_id' => "Product {$product->name} is not available."]);
        }

        $available = $this->inventoryService->availableQuantity(
            $product,
            Carbon::parse($startDate)->toDateString(),
            Carbon::parse($endDate)->toDateString()
        );

        if ($quantity > $available) {
            throw ValidationException::withMessages([
                'quantity' => "Product {$product->name} has only {$available} unit(s) available for the selected dates.",
            ]);
        }
    }

    private function refreshUserCartPricing(Cart $cart, $user): void
    {
        $cart->load('items.product.category');

        if ($cart->items->isEmpty()) {
            return;
        }

        $pricing = $this->pricingService->calculateCart($cart->items, $user);
        $this->pricingService->syncCartPrices($cart->items, $pricing);
    }

    private function calculateRentalDays(string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();

        return (int) $start->diffInDays($end) + 1;
    }

    private function emptySummary(): array
    {
        return [
            'items' => [],
            'rental_subtotal' => 0,
            'deposit_total' => 0,
            'insurance_fee' => 0,
            'shipping_fee' => 0,
            'discount_total' => 0,
            'total_amount' => 0,
            'discounts' => [],
        ];
    }
}
