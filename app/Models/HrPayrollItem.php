<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrPayrollItem extends Model
{
    use HasFactory;

    protected $table = 'hr_payroll_items';

    protected $fillable = [
    'payroll_run_id',
    'employee_id',
    'basic_salary',
    'housing_allowance',
    'transport_allowance',
    'other_allowances',
    'deductions',
    'details',
    'net_salary',
    'notes',
];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'details' => 'array',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(HrPayrollRun::class, 'payroll_run_id');
    }

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
