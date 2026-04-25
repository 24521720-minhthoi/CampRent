<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'rental_subtotal',
        'deposit_total',
        'insurance_fee',
        'shipping_fee',
        'discount_total',
        'total_amount',
        'pricing_snapshot',
        'status',
        'address',
        'paid_at',
        'completed_at',
        'cancelled_at',
        'returned_at',
        'refunded_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rental_subtotal' => 'decimal:2',
        'deposit_total' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'pricing_snapshot' => 'array',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'returned_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function evidences() { return $this->hasMany(OrderEvidence::class); }
    public function statusHistories() { return $this->hasMany(OrderStatusHistory::class); }
    public function reservations() { return $this->hasMany(InventoryReservation::class); }
    public function discounts() { return $this->hasMany(OrderDiscount::class); }
}
