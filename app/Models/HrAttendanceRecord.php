<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrAttendanceRecord extends Model
{
    use HasFactory;

    protected $table = 'hr_attendance_records';

    protected $fillable = [
        'employee_id',
        'attendance_date',
        'check_in',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_distance_meters',
        'check_out',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_distance_meters',
        'worked_hours',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'worked_hours' => 'decimal:2',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
