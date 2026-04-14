<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderEvidence;
use Illuminate\Http\Request;

class OrderEvidenceController extends Controller
{
    public function index(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if (! $this->canAccessOrder($request, $order)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $evidences = $order->evidences()->with('user')->orderBy('created_at', 'desc')->get();

        return response()->json($evidences);
    }

    public function store(Request $request, $orderId)
    {
        $request->validate([
            'type' => 'required|in:send_package,receive_package,return_package,receive_return',
            'media_url' => 'required|url|max:2048',
            'note' => 'nullable|string|max:2000',
        ]);

        $order = Order::findOrFail($orderId);

        if (! $this->canAccessOrder($request, $order)) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }

        $evidence = OrderEvidence::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'type' => $request->input('type'),
            'media_url' => $request->input('media_url'),
            'note' => $request->input('note'),
        ]);

        return response()->json($evidence, 201);
    }

    private function canAccessOrder(Request $request, Order $order): bool
    {
        $user = $request->user();

        if (! $user) {
            return false;
        }

        if ($user->role === 'admin' || $order->user_id === $user->id) {
            return true;
        }

        if ($user->role !== 'shop') {
            return false;
        }

        return $order->items()
            ->whereHas('product', fn ($query) => $query->where('shop_id', $user->id))
            ->exists();
    }
}
