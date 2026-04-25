<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Services\CheckoutService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private OrderStatusService $statusService,
    ) {
    }

    public function checkoutCard(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'promotion_code' => 'nullable|string|max:64',
        ]);

        $order = null;

        try {
            $result = $this->checkoutService->createOrderFromCart(
                $request->user(),
                $validated['address'],
                'card',
                $validated['promotion_code'] ?? null
            );

            $order = $result['order'];
            $payment = $order->payment;
            $stripe = new StripeClient(config('cashier.secret'));

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'vnd',
                        'product_data' => ['name' => "CampRent Order #{$order->id}"],
                        'unit_amount' => (int) round($order->total_amount),
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'order_id' => $order->id,
                    'payment_id' => $payment->id,
                    'user_id' => $request->user()->id,
                ],
                'success_url' => env('FRONTEND_URL') . '/checkout/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('FRONTEND_URL') . '/checkout/cancel',
                'customer_email' => $request->user()->email,
            ]);

            return response()->json([
                'url' => $session->url,
                'order_id' => $order->id,
                'pricing' => $result['pricing'],
            ]);
        } catch (\Throwable $e) {
            if ($order instanceof Order && $order->status === OrderStatusService::PENDING) {
                $this->statusService->transition($order, OrderStatusService::CANCELLED, $request->user(), 'stripe_session_failed');
            }

            throw $e;
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderId = $session->metadata->order_id ?? null;
            $paymentId = $session->metadata->payment_id ?? null;

            if ($orderId && $paymentId) {
                DB::transaction(function () use ($orderId, $paymentId) {
                    $order = Order::find($orderId);
                    $payment = Payment::find($paymentId);

                    if (! $order || ! $payment) {
                        return;
                    }

                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $order->update(['paid_at' => now()]);

                    if ($order->status === OrderStatusService::PENDING) {
                        $this->statusService->transition($order, OrderStatusService::CONFIRMED, null, 'stripe_payment_completed');
                    }

                    CartItem::whereHas('cart', fn ($query) => $query->where('user_id', $order->user_id))->delete();
                });
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function checkoutCash(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'promotion_code' => 'nullable|string|max:64',
        ]);

        $result = $this->checkoutService->createOrderFromCart(
            $request->user(),
            $validated['address'],
            'cash',
            $validated['promotion_code'] ?? null
        );

        CartItem::whereHas('cart', fn ($query) => $query->where('user_id', $request->user()->id))->delete();

        return response()->json([
            'message' => 'Rental order created. Please pay when receiving the items.',
            'order_id' => $result['order']->id,
            'pricing' => $result['pricing'],
            'order' => $result['order'],
        ]);
    }
}
