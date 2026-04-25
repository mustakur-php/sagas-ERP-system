<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StationNozzle extends Model
{
    protected $fillable = [
        'station_id',
        'fuel_type_id',
        'pump_number',
        'nozzle_number',
        'name',
        'last_meter_reading',
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

    public function dailyClosingReadings()
    {
        return $this->hasMany(DailyClosingNozzle::class, 'station_nozzle_id');
    }
}