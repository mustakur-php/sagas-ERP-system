<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyClosingMeterReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_closing_id',
        'station_nozzle_id',
        'fuel_type_id',
        'previous_reading',
        'current_reading',
        'liters_sold',
        'price_per_liter',
        'sales_amount',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class);
    }

    public function stationNozzle()
    {
        return $this->belongsTo(StationNozzle::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}