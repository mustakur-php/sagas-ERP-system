<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelOrderItem extends Model
{
    protected $fillable = [
        'fuel_order_id',
        'fuel_type_id',
        'requested_quantity',
        'approved_quantity',
        'received_quantity',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'approved_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
    ];

    public function fuelOrder()
    {
        return $this->belongsTo(FuelOrder::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

}