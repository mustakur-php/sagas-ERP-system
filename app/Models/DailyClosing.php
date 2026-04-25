<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'closing_date',
        'total_sales',
        'total_collections',
        'total_expenses',
        'notes',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function collections()
    {
        return $this->hasMany(DailyClosingCollection::class);
    }

    public function expenses()
    {
        return $this->hasMany(DailyClosingExpense::class);
    }

    public function nozzleReadings()
    {
        return $this->hasMany(DailyClosingNozzle::class);
    }

    public function getDifferenceAttribute()
    {
        return (float) $this->total_sales - (float) $this->total_collections - (float) $this->total_expenses;
    }

    public function fuelSummaries()
    {
        return $this->hasMany(DailyClosingFuelSummary::class);
    }

}