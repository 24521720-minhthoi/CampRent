<?php

namespace App\Services;

use App\Events\OrderStatusChanged;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const PACKING = 'packing';
    public const SHIPPING = 'shipping';
    public const DELIVERED = 'delivered';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const RETURNED = 'returned';
    public const REFUNDED = 'refunded';

    public const STATUSES = [
        self::PENDING,
        self::CONFIRMED,
        self::PACKING,
        self::SHIPPING,
        self::DELIVERED,
        self::COMPLETED,
        self::CANCELLED,
        self::RETURNED,
        self::REFUNDED,
    ];

    private const TRANSITIONS = [
        self::PENDING => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::PACKING, self::CANCELLED],
        self::PACKING => [self::SHIPPING, self::CANCELLED],
        self::SHIPPING => [self::DELIVERED],
        self::DELIVERED => [self::COMPLETED, self::RETURNED],
        self::COMPLETED => [self::RETURNED],
        self::RETURNED => [self::REFUNDED],
        self::CANCELLED => [],
        self::REFUNDED => [],
    ];

    public function allowedNextStatuses(string $status): array
    {
        return self::TRANSITIONS[$status] ?? [];
    }

    public function assertKnownStatus(string $status): void
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => "Unsupported order status: {$status}",
            ]);
        }
    }

    public function transition(Order $order, string $newStatus, ?User $actor = null, ?string $reason = null, array $metadata = []): Order
    {
        $this->assertKnownStatus($newStatus);
        $oldStatus = $order->status;

        if ($oldStatus === $newStatus) {
            return $order->loadMissing(['statusHistories.actor', 'items.product', 'payment']);
        }

        if (! in_array($newStatus, $this->allowedNextStatuses($oldStatus), true)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition order from {$oldStatus} to {$newStatus}.",
            ]);
        }

        return DB::transaction(function () use ($order, $oldStatus, $newStatus, $actor, $reason, $metadata) {
            $order->forceFill($this->timestampedStatusPayload($newStatus))->save();

            $this->recordHistory($order, $oldStatus, $newStatus, $actor, $reason, $metadata);
            $this->recordInternalNotification($order, $oldStatus, $newStatus);
            $this->syncReservationState($order, $newStatus);

            event(new OrderStatusChanged($order->fresh(), $oldStatus, $newStatus, $actor, $reason));

            return $order->fresh(['user', 'items.product', 'payment', 'statusHistories.actor', 'discounts']);
        });
    }

    public function recordInitialStatus(Order $order, ?User $actor = null, ?string $reason = null, array $metadata = []): void
    {
        $this->recordHistory($order, null, $order->status, $actor, $reason, $metadata);
    }

    private function recordHistory(Order $order, ?string $oldStatus, string $newStatus, ?User $actor, ?string $reason, array $metadata): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $actor?->id,
            'actor_role' => $actor?->role,
            'reason' => $reason,
            'metadata' => $metadata ?: null,
        ]);
    }

    private function recordInternalNotification(Order $order, string $oldStatus, string $newStatus): void
    {
        DB::table('internal_notifications')->insert([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'type' => 'order_status_changed',
            'title' => "Order #{$order->id} status updated",
            'message' => "Your order changed from {$oldStatus} to {$newStatus}.",
            'data' => json_encode([
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function timestampedStatusPayload(string $status): array
    {
        $payload = ['status' => $status];

        if ($status === self::COMPLETED) {
            $payload['completed_at'] = now();
        }

        if ($status === self::CANCELLED) {
            $payload['cancelled_at'] = now();
        }

        if ($status === self::RETURNED) {
            $payload['returned_at'] = now();
        }

        if ($status === self::REFUNDED) {
            $payload['refunded_at'] = now();
        }

        return $payload;
    }

    private function syncReservationState(Order $order, string $status): void
    {
        if ($status === self::CANCELLED || $status === self::REFUNDED) {
            $order->reservations()->update(['status' => InventoryReservation::STATUS_RELEASED]);
            return;
        }

        if ($status === self::COMPLETED || $status === self::RETURNED) {
            $order->reservations()->update(['status' => InventoryReservation::STATUS_COMPLETED]);
        }
    }
}
