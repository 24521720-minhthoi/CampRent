<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private const STATUS_LABELS = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'packing' => 'Packing',
        'shipping' => 'Shipping',
        'delivered' => 'Delivered',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'returned' => 'Returned',
        'refunded' => 'Refunded',
    ];

    public function data(Request $request): array
    {
        [$start, $end, $groupBy] = $this->resolveRange($request);

        $paidPayments = DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->where('payments.status', 'completed')
            ->whereBetween('payments.created_at', [$start, $end]);

        $totalRevenue = (clone $paidPayments)->sum('payments.amount');

        return [
            'stats' => [
                'totalUsers' => (int) DB::table('users')->count(),
                'totalOrders' => (int) DB::table('orders')->whereBetween('created_at', [$start, $end])->count(),
                'totalRevenue' => (string) $totalRevenue,
                'pendingOrders' => (int) DB::table('orders')->where('status', 'pending')->count(),
            ],
            'revenueData' => $this->revenueData($start, $end, $groupBy),
            'orderStatusData' => $this->orderStatusData($start, $end),
            'topProductsData' => $this->bestSellers($start, $end),
            'bestSellers' => $this->bestSellers($start, $end),
            'topCustomersBySpend' => $this->topCustomersBySpend($start, $end),
            'topCustomersByOrderCount' => $this->topCustomersByOrderCount($start, $end),
            'userGrowthData' => $this->userGrowthData($start, $end, $groupBy),
            'filters' => [
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'group_by' => $groupBy,
            ],
        ];
    }

    private function revenueData(Carbon $start, Carbon $end, string $groupBy): array
    {
        return $this->periodBuckets($start, $end, $groupBy)->map(function (array $bucket) {
            $payments = DB::table('payments')
                ->join('orders', 'orders.id', '=', 'payments.order_id')
                ->where('payments.status', 'completed')
                ->whereBetween('payments.created_at', [$bucket['start'], $bucket['end']]);

            return [
                'period' => $bucket['label'],
                'month' => $bucket['label'],
                'revenue' => (float) (clone $payments)->sum('payments.amount'),
                'orders' => (int) (clone $payments)->distinct('orders.id')->count('orders.id'),
            ];
        })->values()->all();
    }

    private function orderStatusData(Carbon $start, Carbon $end): array
    {
        $counts = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as value'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('value', 'status');

        $colors = ['--chart-1', '--chart-2', '--chart-3', '--chart-4', '--chart-5', '--chart-1', '--chart-2', '--chart-3', '--chart-4'];

        return collect(self::STATUS_LABELS)->map(function (string $label, string $status) use ($counts, $colors) {
            $index = array_search($status, array_keys(self::STATUS_LABELS), true) ?: 0;

            return [
                'status' => $label,
                'code' => $status,
                'value' => (int) ($counts[$status] ?? 0),
                'color' => 'var(' . $colors[$index] . ')',
            ];
        })->values()->all();
    }

    private function bestSellers(Carbon $start, Carbon $end): array
    {
        return DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as sales'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->whereIn('orders.status', ['delivered', 'completed', 'returned'])
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('sales')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'sales' => (int) $row->sales,
                'revenue' => (float) $row->revenue,
            ])
            ->all();
    }

    private function topCustomersBySpend(Carbon $start, Carbon $end): array
    {
        return DB::table('payments')
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('SUM(payments.amount) as total_spent'), DB::raw('COUNT(DISTINCT orders.id) as order_count'))
            ->where('payments.status', 'completed')
            ->whereBetween('payments.created_at', [$start, $end])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'total_spent' => (float) $row->total_spent,
                'order_count' => (int) $row->order_count,
            ])
            ->all();
    }

    private function topCustomersByOrderCount(Carbon $start, Carbon $end): array
    {
        return DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.id', 'users.name', 'users.email', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(orders.total_amount) as total_spent'))
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => $row->name,
                'email' => $row->email,
                'order_count' => (int) $row->order_count,
                'total_spent' => (float) $row->total_spent,
            ])
            ->all();
    }

    private function userGrowthData(Carbon $start, Carbon $end, string $groupBy): array
    {
        return $this->periodBuckets($start, $end, $groupBy)->map(function (array $bucket) {
            return [
                'period' => $bucket['label'],
                'month' => $bucket['label'],
                'users' => (int) DB::table('users')->whereBetween('created_at', [$bucket['start'], $bucket['end']])->count(),
            ];
        })->values()->all();
    }

    private function resolveRange(Request $request): array
    {
        $period = $request->query('period', 'month');
        $groupBy = in_array($request->query('group_by'), ['day', 'month', 'year'], true)
            ? $request->query('group_by')
            : ($period === 'year' ? 'month' : 'day');

        if ($request->filled(['start_date', 'end_date'])) {
            return [
                Carbon::parse($request->query('start_date'))->startOfDay(),
                Carbon::parse($request->query('end_date'))->endOfDay(),
                $groupBy,
            ];
        }

        return match ($period) {
            'day' => [now()->startOfDay(), now()->endOfDay(), 'day'],
            'year' => [now()->startOfYear(), now()->endOfYear(), 'month'],
            default => [now()->startOfMonth(), now()->endOfMonth(), 'day'],
        };
    }

    private function periodBuckets(Carbon $start, Carbon $end, string $groupBy): \Illuminate\Support\Collection
    {
        $cursor = $start->copy();
        $buckets = collect();

        while ($cursor->lte($end)) {
            $bucketStart = $cursor->copy()->startOf($groupBy);
            $bucketEnd = $cursor->copy()->endOf($groupBy);

            if ($bucketStart->lt($start)) {
                $bucketStart = $start->copy();
            }

            if ($bucketEnd->gt($end)) {
                $bucketEnd = $end->copy();
            }

            $buckets->push([
                'start' => $bucketStart,
                'end' => $bucketEnd,
                'label' => match ($groupBy) {
                    'year' => $bucketStart->format('Y'),
                    'month' => $bucketStart->format('Y-m'),
                    default => $bucketStart->format('Y-m-d'),
                },
            ]);

            $cursor = $bucketEnd->copy()->addSecond();
        }

        return $buckets;
    }
}
