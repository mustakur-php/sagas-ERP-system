<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyClosingExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_closing_id',
        'expense_type',
        'description',
        'amount',
        'notes',
    ];

    public function dailyClosing()
    {
        return $this->belongsTo(DailyClosing::class);
    }
}