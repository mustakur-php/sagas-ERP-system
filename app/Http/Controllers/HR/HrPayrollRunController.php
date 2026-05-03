<?php

namespace App\Http\Controllers\HR;


use App\Http\Controllers\Controller;
use App\Models\HrPayrollRun;
use App\Models\HrPayrollItem;
use App\Models\HrEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use App\Models\HrEmployeeAdvance;
use App\Models\HrAttendanceRecord;
use App\Models\HrPayrollPolicy;



class HrPayrollRunController extends Controller
{
    public function index()
    {
        $payrollRuns = HrPayrollRun::latest()->paginate(10);

        return view('hr.payroll.index', compact('payrollRuns'));
    }

    public function create()
    {
        return view('hr.payroll.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'company_id' => ['nullable', 'integer'],
        ]);

        $exists = HrPayrollRun::where('company_id', $request->company_id)
            ->where('year', $request->year)
            ->where('month', $request->month)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'مسير الرواتب لهذا الشهر موجود مسبقًا.');
        }

        DB::transaction(function () use ($request) {

            $run = HrPayrollRun::create([
                'company_id' => $request->company_id,
                'year' => $request->year,
                'month' => $request->month,
                'status' => 'draft',
            ]);

            $employees = HrEmployee::with(['contracts' => function ($q) {
                $q->where('status', 'active')
                    ->whereDate('start_date', '<=', now())
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', now());
                    })
                    ->latest();
            }])->get();

            $totalBasic = 0;
            $totalAllowances = 0;
            $totalDeductions = 0;
            $netAmount = 0;

            foreach ($employees as $employee) {
                $contract = $employee->contracts->first();

                if (!$contract) {
                    continue;
                }
                $basic = $contract->basic_salary;
                $housing = $contract->housing_allowance;
                $transport = $contract->transport_allowance;
                $other = $contract->other_allowances;
                $allowances = $housing + $transport + $other;

                $policy = HrPayrollPolicy::first();

                $standardDailyHours = $policy?->standard_daily_hours ?? 8;
                $monthlyWorkingDays = $policy?->monthly_working_days ?? 30;
                $latePerMinute = $policy?->late_deduction_per_minute ?? 0;
                $earlyPerMinute = $policy?->early_leave_deduction_per_minute ?? 0;
                $overtimeMultiplier = $policy?->overtime_rate_multiplier ?? 1.5;
                $overtimeEnabled = $policy?->overtime_enabled ?? true;

                $hourRate = $basic / $monthlyWorkingDays / $standardDailyHours;
                $minuteRate = $hourRate / 60;

                $attendanceRecords = HrAttendanceRecord::where('employee_id', $employee->id)
                    ->whereYear('attendance_date', $request->year)
                    ->whereMonth('attendance_date', $request->month)
                    ->get();

                $lateDeduction = 0;
                $earlyDeduction = 0;
                $overtimeAmount = 0;

                foreach ($attendanceRecords as $record) {

                    // خصم التأخير
                    if ($record->late_minutes > 0) {
                        $lateDeduction += $record->late_minutes * $latePerMinute;
                    }

                    // خصم الخروج المبكر
                    if ($record->early_leave_minutes > 0) {
                        $earlyDeduction += $record->early_leave_minutes * $earlyPerMinute;
                    }

                    // الإضافي
                    if ($overtimeEnabled && $record->overtime_minutes > 0) {
                        $overtimeAmount += $record->overtime_minutes * $minuteRate * $overtimeMultiplier;
                    }
                }
                // خصم الإجازات غير المدفوعة
                $leaves = HrLeaveRequest::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->whereMonth('start_date', $request->month)
                    ->whereYear('start_date', $request->year)
                    ->get();

                $leaveDeduction = 0;

                foreach ($leaves as $leave) {
                    $leaveType = HrLeaveType::find($leave->leave_type_id);

                    if ($leaveType && !$leaveType->is_paid) {
                        $dailySalary = $basic / 30;
                        $leaveDeduction += $dailySalary * $leave->days_count;
                    }
                }

                // خصم السلف المعتمدة في نفس الشهر
                $advanceDeduction = HrEmployeeAdvance::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->whereYear('advance_date', $request->year)
                    ->whereMonth('advance_date', $request->month)
                    ->sum('amount');

                $deductions =
                    $leaveDeduction +
                    $advanceDeduction +
                    $lateDeduction +
                    $earlyDeduction;

                $netSalary = $basic + $allowances + $overtimeAmount - $deductions;

                HrPayrollItem::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'basic_salary' => $basic,
                    'housing_allowance' => $housing,
                    'transport_allowance' => $transport,
                    'other_allowances' => $other,
                    'allowances' => $allowances,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'details' => [
                        'leave_deduction' => $leaveDeduction,
                        'advance_deduction' => $advanceDeduction,
                        'late_deduction' => $lateDeduction,
                        'early_deduction' => $earlyDeduction,
                        'overtime_amount' => $overtimeAmount,
                        'late_minutes' => $attendanceRecords->sum('late_minutes'),
                        'early_leave_minutes' => $attendanceRecords->sum('early_leave_minutes'),
                        'overtime_minutes' => $attendanceRecords->sum('overtime_minutes'),
                    ],
                ]);

                HrEmployeeAdvance::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->whereYear('advance_date', $request->year)
                    ->whereMonth('advance_date', $request->month)
                    ->update(['status' => 'deducted']);

                $totalBasic += $basic;
                $totalAllowances += $allowances;
                $totalDeductions += $deductions;
                $netAmount += $netSalary;
            }

            $run->update([
                'total_basic_salary' => $totalBasic,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'net_amount' => $netAmount,
            ]);
        });

        return redirect()
            ->route('hr.payroll.index')
            ->with('success', 'تم توليد مسير الرواتب بنجاح.');
    }
    
    public function show(HrPayrollRun $payroll)
    {
        $payroll->load('items.employee');

        return view('hr.payroll.show', compact('payroll'));
    }

}