<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrContract extends Model
{
    use HasFactory;

    protected $table = 'hr_contracts';

    protected $fillable = [
        'employee_id',
        'contract_number',
        'contract_type',
        'start_date',
        'end_date',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowances',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
