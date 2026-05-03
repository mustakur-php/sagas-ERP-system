<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrEmployeeAdvance;
use Illuminate\Http\Request;

class HrEmployeeAdvanceController extends Controller
{
    public function index()
    {
        $advances = HrEmployeeAdvance::with('employee')
            ->latest()
            ->paginate(10);

        return view('hr.advances.index', compact('advances'));
    }

    public function create()
    {
        $employees = HrEmployee::orderBy('name_ar')->get();

        return view('hr.advances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'advance_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,approved,rejected,deducted'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        HrEmployeeAdvance::create([
            'employee_id' => $request->employee_id,
            'amount' => $request->amount,
            'advance_date' => $request->advance_date,
            'status' => $request->status,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'approved_by' => $request->status === 'approved' ? auth()->id() : null,
            'approved_at' => $request->status === 'approved' ? now() : null,
        ]);

        return redirect()
            ->route('hr.advances.index')
            ->with('success', 'تم إضافة السلفة بنجاح.');
    }

    public function edit(HrEmployeeAdvance $advance)
    {
        $employees = HrEmployee::orderBy('name_ar')->get();

        return view('hr.advances.edit', compact('advance', 'employees'));
    }

    public function update(Request $request, HrEmployeeAdvance $advance)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'advance_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,approved,rejected,deducted'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $advance->update([
            'employee_id' => $request->employee_id,
            'amount' => $request->amount,
            'advance_date' => $request->advance_date,
            'status' => $request->status,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'approved_by' => $request->status === 'approved' ? auth()->id() : $advance->approved_by,
            'approved_at' => $request->status === 'approved' ? now() : $advance->approved_at,
        ]);

        return redirect()
            ->route('hr.advances.index')
            ->with('success', 'تم تحديث السلفة بنجاح.');
    }

    public function destroy(HrEmployeeAdvance $advance)
    {
        $advance->delete();

        return redirect()
            ->route('hr.advances.index')
            ->with('success', 'تم حذف السلفة بنجاح.');
    }
}