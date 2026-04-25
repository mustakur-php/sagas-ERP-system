<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyClosingFuelSummary extends Model
{
    protected $fillable = [
        'daily_closing_id',
        'fuel_type_id',
        'total_liters',
        'returned_liters',
        'net_liters',
        'price_per_liter',
        'net_amount',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}
