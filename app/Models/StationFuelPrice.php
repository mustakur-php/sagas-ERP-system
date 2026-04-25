<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationFuelPrice extends Model
{
    protected $fillable = [
        'station_id',
        'fuel_type_id',
        'price_per_liter'
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}