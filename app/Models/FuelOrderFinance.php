<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelOrderFinance extends Model
{
    protected $fillable = [
        'fuel_order_id',
        'status',
        'amount',
        'payment_reference',
        'reviewed_by',
        'reviewed_at',
        'paid_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(FuelOrder::class, 'fuel_order_id');
    }
}
