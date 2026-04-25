<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $appends = ['pricing'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'deposit_amount',
        'stock',
        'image_url',
        'images',
        'status',
        'category_id',
        'shop_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'images' => 'array',
    ];

    // Một product thuộc về một category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Một product thuộc về một shop
    public function shop()
    {
        return $this->belongsTo(User::class);
    }

    // Lịch sử thay đổi giá của sản phẩm
    public function priceHistories()
    {
        return $this->hasMany(ProductPriceHistory::class)->orderBy('created_at', 'desc');
    }

    // Lần thay đổi giá gần nhất
    public function latestPriceChange()
    {
        return $this->hasOne(ProductPriceHistory::class)->latestOfMany();
    }

    // Kiểm tra sản phẩm có cần đặt cọc không
    public function requiresDeposit(): bool
    {
        return $this->deposit_amount > 0;
    }

    public function promotionTargets()
    {
        return $this->hasMany(PromotionTarget::class, 'target_id')
            ->where('target_type', 'product');
    }

    public function getPricingAttribute(): array
    {
        $basePrice = (float) $this->price;
        $pricing = [
            'base_price' => $basePrice,
            'final_price' => $basePrice,
            'discount_amount' => 0.0,
            'discount_percent' => 0.0,
            'sale_badge' => false,
            'promotion' => null,
        ];

        if (! $this->exists) {
            return $pricing;
        }

        try {
            $promotionPricing = app(\App\Services\PromotionService::class)
                ->bestProductPromotionPreview($this, $basePrice);

            return array_merge($pricing, $promotionPricing);
        } catch (\Throwable) {
            return $pricing;
        }
    }
}
