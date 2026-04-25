<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyClosingNozzle extends Model
{
    protected $fillable = [
        'daily_closing_id',
        'station_nozzle_id',
        'start_reading',
        'end_reading',
        'total_liters',
        'price',
        'total_amount',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class);
    }

    public function stationNozzle()
    {
        return $this->belongsTo(StationNozzle::class, 'station_nozzle_id');
    }
}