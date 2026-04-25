<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'old_status',
        'new_status',
        'changed_by',
        'actor_role',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function actor() { return $this->belongsTo(User::class, 'changed_by'); }
}
