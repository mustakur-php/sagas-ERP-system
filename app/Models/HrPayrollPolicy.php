<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrPayrollPolicy extends Model
{
    protected $table = 'hr_payroll_policies';

    protected $fillable = [
        'company_id',
        'standard_daily_hours',
        'monthly_working_days',
        'late_grace_minutes',
        'late_deduction_per_minute',
        'early_leave_deduction_per_minute',
        'overtime_enabled',
        'overtime_rate_multiplier',
        'absence_deduction_type',
    ];

    protected $casts = [
        'standard_daily_hours' => 'decimal:2',
        'overtime_rate_multiplier' => 'decimal:2',
        'overtime_enabled' => 'boolean',
    ];
}