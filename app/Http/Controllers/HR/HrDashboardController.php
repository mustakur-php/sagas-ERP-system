<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\HrEmployee;
use App\Models\HrContract;
use App\Models\HrLeaveRequest;
use App\Models\HrAttendanceRecord;
use App\Models\HrActivity;
use App\Models\HrSetting;
use App\Models\HrDepartment;
use App\Models\HrPosition;
use App\Models\HrPayrollRun;

class HrDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'overview');

        $today = Carbon::today();

        $totalEmployees = HrEmployee::count();
        $activeContracts = HrContract::where('status', 'active')->count();
        $pendingRequests = HrLeaveRequest::where('status', 'pending')->count();

        $onLeaveToday = HrLeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $stats = [
            [
                'label' => 'إجمالي الموظفين',
                'value' => $totalEmployees,
                'trend' => '',
                'color' => 'blue',
                'icon' => 'users',
            ],
            [
                'label' => 'العقود النشطة',
                'value' => $activeContracts,
                'trend' => '',
                'color' => 'emerald',
                'icon' => 'file',
            ],
            [
                'label' => 'طلبات معلقة',
                'value' => $pendingRequests,
                'trend' => '',
                'color' => 'amber',
                'icon' => 'bell',
            ],
            [
                'label' => 'في إجازة اليوم',
                'value' => $onLeaveToday,
                'trend' => '',
                'color' => 'rose',
                'icon' => 'clock',
            ],
        ];

        $attendance = collect(range(6, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);

            return [
                'name' => $date->format('D'),
                'present' => HrAttendanceRecord::whereDate('attendance_date', $date)
                    ->where('status', 'present')
                    ->count(),
                'absent' => HrAttendanceRecord::whereDate('attendance_date', $date)
                    ->where('status', 'absent')
                    ->count(),
            ];
        })->values();

        $recentActivities = HrActivity::latest()
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'type' => $activity->type ?? 'activity',
                    'user' => $activity->user_name ?? $activity->user ?? 'System',
                    'action' => $activity->action ?? $activity->description ?? '-',
                    'time' => optional($activity->created_at)->diffForHumans(),
                ];
            });

        if ($recentActivities->isEmpty()) {
            $recentActivities = collect([
                [
                    'id' => 1,
                    'type' => 'hiring',
                    'user' => 'System',
                    'action' => 'لا توجد أنشطة مسجلة حتى الآن',
                    'time' => '',
                ],
            ]);
        }

        $settings = HrSetting::orderBy('id')->get();

        if ($settings->isEmpty()) {
            $settings = collect([
                [
                    'label' => 'ملف الشركة',
                    'desc' => 'بيانات الشركة والعنوان والرقم الضريبي',
                    'icon' => 'building',
                ],
                [
                    'label' => 'جدول العمل',
                    'desc' => 'إعدادات الدوام والشفتات والعمل الإضافي',
                    'icon' => 'clock',
                ],
                [
                    'label' => 'سياسة الإجازات',
                    'desc' => 'إدارة الإجازات السنوية والمرضية',
                    'icon' => 'file',
                ],
                [
                    'label' => 'الرواتب',
                    'desc' => 'إعدادات الرواتب والتأمينات والبدلات',
                    'icon' => 'wallet',
                ],
                [
                    'label' => 'الصلاحيات والأدوار',
                    'desc' => 'إدارة صلاحيات مستخدمي الموارد البشرية',
                    'icon' => 'users',
                ],
            ]);
        }

        $employees = HrEmployee::latest()->limit(10)->get();
        $departments = HrDepartment::withCount('employees')->latest()->limit(10)->get();
        $positions = HrPosition::latest()->limit(10)->get();
        $payrollRuns = HrPayrollRun::latest()->limit(10)->get();
        $leaveRequests = HrLeaveRequest::latest()->limit(10)->get();

        return view('hr.index', compact(
            'tab',
            'stats',
            'attendance',
            'recentActivities',
            'settings',
            'employees',
            'departments',
            'positions',
            'payrollRuns',
            'leaveRequests'
        ));
    }
}