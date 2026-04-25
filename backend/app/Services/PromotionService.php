<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDiscount;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionTarget;
use App\Models\PromotionUsage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PromotionService
{
    public function bestProductPromotionPreview(Product $product, float $unitPrice): array
    {
        $promotion = $this->automaticPromotions()
            ->first(fn (Promotion $promotion) => $this->appliesToProduct($promotion, $product));

        if (! $promotion) {
            return [];
        }

        $discount = $this->calculateDiscount($promotion, $unitPrice, 1, 1);
        $finalPrice = max(0, $unitPrice - $discount);

        return [
            'base_price' => $unitPrice,
            'final_price' => $finalPrice,
            'discount_amount' => $discount,
            'discount_percent' => $unitPrice > 0 ? round(($discount / $unitPrice) * 100, 2) : 0,
            'sale_badge' => $discount > 0,
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'type' => $promotion->type,
                'value' => (float) $promotion->value,
            ],
        ];
    }

    public function applyBestAutomaticPromotion(Product $product, ?User $user, float $baseAmount, int $quantity, int $days): ?array
    {
        $best = null;

        foreach ($this->automaticPromotions() as $promotion) {
            if (! $this->appliesToProduct($promotion, $product)) {
                continue;
            }

            if (! $this->canUse($promotion, $user, $baseAmount)) {
                continue;
            }

            $discount = $this->calculateDiscount($promotion, $baseAmount, $quantity, $days, (float) $product->price);

            if ($discount > ($best['amount'] ?? 0)) {
                $best = $this->discountPayload($promotion, $discount, 'item');
            }
        }

        return $best;
    }

    public function applyVoucher(string $code, ?User $user, Collection $pricedItems, float $discountableSubtotal): ?array
    {
        $promotion = Promotion::currentlyActive()
            ->where('code', Str::upper(trim($code)))
            ->with('targets')
            ->first();

        if (! $promotion) {
            throw ValidationException::withMessages(['promotion_code' => 'Promotion code is invalid or expired.']);
        }

        $this->assertCanUse($promotion, $user, $discountableSubtotal);

        $eligibleSubtotal = $pricedItems
            ->filter(fn (array $item) => $this->appliesToProduct($promotion, $item['product']))
            ->sum(fn (array $item) => max(0, $item['rental_subtotal'] - $item['discount_amount']));

        if ($eligibleSubtotal <= 0) {
            throw ValidationException::withMessages(['promotion_code' => 'Promotion code is not applicable to this cart.']);
        }

        $discount = $this->calculateDiscount($promotion, $eligibleSubtotal, 1, 1);

        return $this->discountPayload($promotion, $discount, 'order');
    }

    public function recordUsages(Order $order, array $pricing, User $user): void
    {
        foreach ($pricing['discounts'] ?? [] as $discount) {
            if (empty($discount['promotion_id']) || $discount['amount'] <= 0) {
                continue;
            }

            OrderDiscount::create([
                'order_id' => $order->id,
                'promotion_id' => $discount['promotion_id'],
                'code' => $discount['code'] ?? null,
                'type' => $discount['type'],
                'value' => $discount['value'],
                'amount' => $discount['amount'],
                'snapshot' => $discount,
            ]);

            PromotionUsage::firstOrCreate(
                [
                    'promotion_id' => $discount['promotion_id'],
                    'order_id' => $order->id,
                ],
                [
                    'user_id' => $user->id,
                    'code' => $discount['code'] ?? null,
                    'discount_amount' => $discount['amount'],
                ]
            );
        }
    }

    public function appliesToProduct(Promotion $promotion, Product $product): bool
    {
        if ($promotion->scope === Promotion::SCOPE_ALL) {
            return true;
        }

        $targets = $promotion->relationLoaded('targets')
            ? $promotion->targets
            : $promotion->targets()->get();

        if ($targets->isEmpty()) {
            return false;
        }

        return $targets->contains(function (PromotionTarget $target) use ($promotion, $product) {
            return match ($promotion->scope) {
                Promotion::SCOPE_PRODUCT => $target->target_type === 'product' && (int) $target->target_id === (int) $product->id,
                Promotion::SCOPE_CATEGORY => $target->target_type === 'category' && (int) $target->target_id === (int) $product->category_id,
                Promotion::SCOPE_SHOP => $target->target_type === 'shop' && (int) $target->target_id === (int) $product->shop_id,
                default => false,
            };
        });
    }

    public function assertCanUse(Promotion $promotion, ?User $user, float $orderSubtotal): void
    {
        if (! $this->canUse($promotion, $user, $orderSubtotal)) {
            throw ValidationException::withMessages([
                'promotion_code' => 'Promotion usage conditions are not met.',
            ]);
        }
    }

    public function canUse(Promotion $promotion, ?User $user, float $orderSubtotal): bool
    {
        if ((float) $promotion->min_order_value > $orderSubtotal) {
            return false;
        }

        if ($promotion->usage_limit !== null && $promotion->usages()->count() >= $promotion->usage_limit) {
            return false;
        }

        if (! $user) {
            return ! $promotion->new_user_only && $promotion->per_user_limit === null;
        }

        if ($promotion->per_user_limit !== null
            && $promotion->usages()->where('user_id', $user->id)->count() >= $promotion->per_user_limit) {
            return false;
        }

        if ($promotion->new_user_only && $user->orders()->exists()) {
            return false;
        }

        return true;
    }

    private function automaticPromotions(): EloquentCollection
    {
        return Promotion::currentlyActive()
            ->whereNull('code')
            ->with('targets')
            ->orderByDesc('value')
            ->get();
    }

    private function calculateDiscount(Promotion $promotion, float $baseAmount, int $quantity, int $days, ?float $unitPrice = null): float
    {
        $discount = match ($promotion->type) {
            Promotion::TYPE_PERCENT => $baseAmount * min(100, (float) $promotion->value) / 100,
            Promotion::TYPE_FIXED => min((float) $promotion->value, $baseAmount),
            Promotion::TYPE_BOGO => $unitPrice ? $unitPrice * $days * floor($quantity / 2) : 0,
            default => 0,
        };

        return round(min($discount, $baseAmount), 2);
    }

    private function discountPayload(Promotion $promotion, float $amount, string $level): array
    {
        return [
            'promotion_id' => $promotion->id,
            'code' => $promotion->code,
            'name' => $promotion->name,
            'type' => $promotion->type,
            'value' => (float) $promotion->value,
            'amount' => round($amount, 2),
            'level' => $level,
        ];
    }
}
