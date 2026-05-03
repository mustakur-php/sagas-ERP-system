<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrLeaveType extends Model
{
    use HasFactory;

    protected $table = 'hr_leave_types';

    protected $fillable = [
        'company_id',
        'name_ar',
        'name_en',
        'annual_balance_days',
        'is_paid',
        'requires_approval',
        'is_active',
    ];

    protected $casts = [
        'annual_balance_days' => 'integer',
        'is_paid' => 'boolean',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveRequests()
    {
        return $this->hasMany(HrLeaveRequest::class, 'leave_type_id');
    }
}
