<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StationTank extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'fuel_type_id',
        'name',
        'capacity',
        'opening_balance',
        'current_quantity',
        'warning_level',
        'critical_level',
        'status',
        'notes',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'station_tank_id');
    }

    public function getFillPercentageAttribute(): float
    {
        if ((float) $this->capacity <= 0) {
            return 0;
        }

        return round(((float) $this->current_quantity / (float) $this->capacity) * 100, 2);
    }

    public function getAvailableSpaceAttribute(): float
    {
        return max((float) $this->capacity - (float) $this->current_quantity, 0);
    }

    public function getLevelStatusAttribute(): string
    {
        $percentage = $this->fill_percentage;

        if ($percentage <= (float) $this->critical_level) {
            return 'critical';
        }

        if ($percentage <= (float) $this->warning_level) {
            return 'warning';
        }

        return 'good';
    }
}