<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyClosingCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_closing_id',
        'collection_type',
        'provider_name',
        'amount',
        'notes',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class);
    }
}