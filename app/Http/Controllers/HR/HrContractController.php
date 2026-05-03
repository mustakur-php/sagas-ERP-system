<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrContract;
use App\Models\HrEmployee;
use Illuminate\Http\Request;


class HrContractController extends Controller
{
    public function index()
    {
        $contracts = HrContract::with('employee')
            ->latest()
            ->paginate(10);

        return view('hr.contracts.index', compact('contracts'));
    }

    public function create()
    {
        $employees = HrEmployee::orderBy('name_ar')->get();

        return view('hr.contracts.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'contract_number' => ['required', 'string', 'max:255', 'unique:hr_contracts,contract_number'],
            'contract_type' => ['required', 'in:full_time,part_time,temporary,probation'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'transport_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowances' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,expired,renewed,terminated'],
            'notes' => ['nullable', 'string'],
        ]);

        HrContract::create([
            'employee_id' => $request->employee_id,
            'contract_number' => $request->contract_number,
            'contract_type' => $request->contract_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'basic_salary' => $request->basic_salary,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'other_allowances' => $request->other_allowances ?? 0,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('hr.contracts.index')
            ->with('success', 'تم إنشاء العقد بنجاح.');
    }

    public function edit(HrContract $contract)
    {
        $employees = HrEmployee::orderBy('name_ar')->get();

        return view('hr.contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, HrContract $contract)
    {
        $request->validate([
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'contract_number' => ['required', 'string', 'max:255', 'unique:hr_contracts,contract_number,' . $contract->id],
            'contract_type' => ['required', 'in:full_time,part_time,temporary,probation'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'transport_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowances' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,expired,renewed,terminated'],
            'notes' => ['nullable', 'string'],
        ]);

        $contract->update([
            'employee_id' => $request->employee_id,
            'contract_number' => $request->contract_number,
            'contract_type' => $request->contract_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'basic_salary' => $request->basic_salary,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'other_allowances' => $request->other_allowances ?? 0,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()
            ->route('hr.contracts.index')
            ->with('success', 'تم تحديث العقد بنجاح.');
    }

    public function destroy(HrContract $contract)
    {
        $contract->delete();

        return redirect()
            ->route('hr.contracts.index')
            ->with('success', 'تم حذف العقد بنجاح.');
    }
}
