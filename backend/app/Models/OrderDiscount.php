<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    protected $fillable = [
        'order_id',
        'promotion_id',
        'code',
        'type',
        'value',
        'amount',
        'snapshot',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'amount' => 'decimal:2',
        'snapshot' => 'array',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function promotion() { return $this->belongsTo(Promotion::class); }
}
