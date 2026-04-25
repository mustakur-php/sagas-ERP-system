<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'station_id',
        'amount',
        'quantity',
        'sale_date',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}