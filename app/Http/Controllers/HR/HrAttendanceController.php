<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrAttendanceRecord;
use App\Models\HrEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HrAttendanceController extends Controller
{
    public function index()
    {
        $employees = HrEmployee::with('workLocation')
            ->orderBy('name_ar')
            ->get();

        $todayRecords = HrAttendanceRecord::with('employee')
            ->whereDate('attendance_date', Carbon::today())
            ->latest()
            ->get();

        return view('hr.attendance.index', compact('employees', 'todayRecords'));
    }

    public function checkIn(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $employee = HrEmployee::with('workLocation')->findOrFail($data['employee_id']);

        $distance = $this->validateEmployeeLocation($employee, $data['latitude'], $data['longitude']);

        $existing = HrAttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if ($existing && $existing->check_in) {
            return back()->withErrors(['attendance' => 'تم تسجيل حضور هذا الموظف اليوم مسبقًا.']);
        }

        HrAttendanceRecord::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => Carbon::today(),
            ],
            [
                'check_in' => Carbon::now()->format('H:i:s'),
                'check_in_latitude' => $data['latitude'],
                'check_in_longitude' => $data['longitude'],
                'check_in_distance_meters' => round($distance),
                'status' => 'present',
            ]
        );

        return back()->with('success', 'تم تسجيل الحضور بنجاح.');
    }

    public function checkOut(Request $request)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $employee = HrEmployee::with('workLocation')->findOrFail($data['employee_id']);

        $distance = $this->validateEmployeeLocation($employee, $data['latitude'], $data['longitude']);

        $record = HrAttendanceRecord::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        if (!$record || !$record->check_in) {
            return back()->withErrors(['attendance' => 'لا يمكن تسجيل الانصراف قبل تسجيل الحضور.']);
        }

        if ($record->check_out) {
            return back()->withErrors(['attendance' => 'تم تسجيل الانصراف لهذا الموظف مسبقًا.']);
        }

        $checkIn = Carbon::parse($record->attendance_date->format('Y-m-d') . ' ' . $record->check_in);
        $checkOut = Carbon::now();

        $workedMinutes = $checkIn->diffInMinutes($checkOut);
        $workedHours = round($workedMinutes / 60, 2);

        $policy = \App\Models\HrPayrollPolicy::first();

        $standardDailyHours = $policy?->standard_daily_hours ?? 8;
        $lateGraceMinutes = $policy?->late_grace_minutes ?? 15;

        $standardMinutes = (int) ($standardDailyHours * 60);

        $expectedStart = Carbon::parse($record->attendance_date->format('Y-m-d') . ' 08:00:00');
        $expectedEnd = (clone $expectedStart)->addMinutes($standardMinutes);

        $lateMinutes = 0;
        if ($checkIn->greaterThan($expectedStart)) {
            $lateMinutes = $expectedStart->diffInMinutes($checkIn);

            if ($lateMinutes <= $lateGraceMinutes) {
                $lateMinutes = 0;
            }
        }

        $earlyLeaveMinutes = 0;
        if ($checkOut->lessThan($expectedEnd)) {
            $earlyLeaveMinutes = $checkOut->diffInMinutes($expectedEnd);
        }

        $overtimeMinutes = 0;
        if ($workedMinutes > $standardMinutes) {
            $overtimeMinutes = $workedMinutes - $standardMinutes;
        }

        $record->update([
            'check_out' => $checkOut->format('H:i:s'),
            'check_out_latitude' => $data['latitude'],
            'check_out_longitude' => $data['longitude'],
            'check_out_distance_meters' => round($distance),
            'worked_hours' => $workedHours,
            'late_minutes' => $lateMinutes,
            'early_leave_minutes' => $earlyLeaveMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ]);

        return back()->with('success', 'تم تسجيل الانصراف بنجاح.');
    }

    private function validateEmployeeLocation(HrEmployee $employee, $latitude, $longitude): float
    {
        if (!$employee->workLocation) {
            abort(back()->withErrors(['location' => 'الموظف غير مربوط بموقع عمل.']));
        }

        if (!$employee->workLocation->is_active) {
            abort(back()->withErrors(['location' => 'موقع العمل المرتبط بالموظف غير نشط.']));
        }

        $distance = $this->distanceInMeters(
            $latitude,
            $longitude,
            $employee->workLocation->latitude,
            $employee->workLocation->longitude
        );

        if ($distance > $employee->workLocation->radius_meters) {
            abort(back()->withErrors([
                'location' => 'أنت خارج نطاق موقع العمل. المسافة الحالية تقريبًا: ' . round($distance) . ' متر.'
            ]));
        }

        return $distance;
    }

    private function distanceInMeters($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000;

        $latFrom = deg2rad((float) $lat1);
        $lonFrom = deg2rad((float) $lon1);
        $latTo = deg2rad((float) $lat2);
        $lonTo = deg2rad((float) $lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2
            + cos($latFrom) * cos($latTo) * sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}