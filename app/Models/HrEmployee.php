<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\HrDepartment;
use App\Models\HrPosition;
use App\Models\HrContract;
use App\Models\HrAttendanceRecord;
use App\Models\HrLeaveRequest;
use App\Models\HrPayrollItem;
use App\Models\HrEmployeeAdvance;


class HrEmployee extends Model
{
    use HasFactory;

    protected $table = 'hr_employees';

    protected $fillable = [
        'company_id',
        'station_id',
        'user_id',
        'department_id',
        'position_id',
        'work_location_id',
        'employee_number',
        'name_ar',
        'name_en',
        'national_id',
        'iqama_number',
        'mobile',
        'email',
        'gender',
        'birth_date',
        'nationality',
        'hire_date',
        'employment_status',
        'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    public function position()
    {
        return $this->belongsTo(HrPosition::class, 'position_id');
    }

    public function contracts()
    {
        return $this->hasMany(HrContract::class, 'employee_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(HrAttendanceRecord::class, 'employee_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(HrLeaveRequest::class, 'employee_id');
    }

    public function payrollItems()
    {
        return $this->hasMany(HrPayrollItem::class, 'employee_id');
    }

    public function latestContract()
    {
        return $this->hasOne(HrContract::class, 'employee_id')->latestOfMany();
    }

    public function workLocation()
    {
        return $this->belongsTo(HrWorkLocation::class, 'work_location_id');
    }

    public function advances()
    {
        return $this->hasMany(HrEmployeeAdvance::class, 'employee_id');
    }
}
