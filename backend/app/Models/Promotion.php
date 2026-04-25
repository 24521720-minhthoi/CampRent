<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    public const TYPE_PERCENT = 'percent';
    public const TYPE_FIXED = 'fixed';
    public const TYPE_BOGO = 'bogo';

    public const SCOPE_ALL = 'all';
    public const SCOPE_CATEGORY = 'category';
    public const SCOPE_PRODUCT = 'product';
    public const SCOPE_SHOP = 'shop';

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'scope',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'min_order_value',
        'new_user_only',
        'status',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'min_order_value' => 'decimal:2',
        'new_user_only' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function targets() { return $this->hasMany(PromotionTarget::class); }
    public function usages() { return $this->hasMany(PromotionUsage::class); }

    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $now = now();

        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where(function (Builder $builder) use ($now) {
                $builder->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function (Builder $builder) use ($now) {
                $builder->whereNull('end_date')->orWhere('end_date', '>=', $now);
            });
    }
}
