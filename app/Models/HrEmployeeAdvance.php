<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrEmployeeAdvance extends Model
{
    use HasFactory;

    protected $table = 'hr_employee_advances';

    protected $fillable = [
        'employee_id',
        'amount',
        'advance_date',
        'status',
        'approved_by',
        'approved_at',
        'reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'advance_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}