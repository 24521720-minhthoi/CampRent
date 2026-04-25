<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'start_date',
        'end_date',
        'price',
        'unit_deposit',
        'days',
        'rental_subtotal',
        'discount_amount',
        'deposit_total',
        'subtotal',
        'total_amount',
        'promotion_id',
        'pricing_snapshot',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'price' => 'decimal:2',
        'unit_deposit' => 'decimal:2',
        'rental_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'deposit_total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'pricing_snapshot' => 'array',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function promotion() { return $this->belongsTo(Promotion::class); }
}
