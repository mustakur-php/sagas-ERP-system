<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = [
        'customer_name',
        'amount',
        'paid_amount',
        'due_date',
        'status',
    ];

    public function getRemainingAttribute()
    {
        return $this->amount - $this->paid_amount;
    }
}