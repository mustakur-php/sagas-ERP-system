<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelOrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_order_id',
        'user_id',
        'action',
        'from_step',
        'to_step',
        'notes',
    ];

    public function fuelOrder()
    {
        return $this->belongsTo(FuelOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}