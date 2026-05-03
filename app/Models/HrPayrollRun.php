<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrPayrollRun extends Model
{
    use HasFactory;

    protected $table = 'hr_payroll_runs';

    protected $fillable = [
        'company_id',
        'year',
        'month',
        'total_basic_salary',
        'total_allowances',
        'total_deductions',
        'net_amount',
        'status',
        'processed_by',
        'processed_at',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'total_basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(HrPayrollItem::class, 'payroll_run_id');
    }
}
