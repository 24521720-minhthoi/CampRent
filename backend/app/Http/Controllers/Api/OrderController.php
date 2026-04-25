<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(private OrderStatusService $statusService)
    {
    }

    public function index(Request $request)
    {
        $orders = Order::with(['user', 'items.product', 'payment', 'discounts', 'statusHistories.actor'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    public function getOrdersForAdmin(Request $request)
    {
        $orders = Order::with(['user', 'items.product', 'payment', 'discounts'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($orders);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['user', 'items.product', 'payment', 'evidences.user', 'statusHistories.actor', 'discounts'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(OrderStatusService::STATUSES)],
            'reason' => 'nullable|string|max:255',
        ]);

        $order = Order::with(['user', 'items.product', 'payment', 'statusHistories.actor'])->findOrFail($id);

        return response()->json($this->statusService->transition(
            $order,
            $validated['status'],
            $request->user(),
            $validated['reason'] ?? null,
            ['source' => 'admin_api']
        ));
    }
}
