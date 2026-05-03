<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrEmployee;
use App\Models\HrDepartment;
use App\Models\HrPosition;
use Illuminate\Http\Request;
use App\Models\HrWorkLocation;

class HrEmployeeController extends Controller
{
    public function index()
    {
        $employees = HrEmployee::with(['department', 'position'])
            ->latest()
            ->paginate(15);

        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = HrDepartment::orderBy('name_ar')->get();
        $positions = HrPosition::orderBy('title_ar')->get();
        $workLocations = HrWorkLocation::where('is_active', true)->orderBy('name')->get();

        return view('hr.employees.create', compact(
            'departments',
            'positions',
            'workLocations'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', 'unique:hr_employees,employee_number'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:hr_employees,email'],
            'mobile' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'iqama_number' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:hr_departments,id'],
            'position_id' => ['nullable', 'exists:hr_positions,id'],
            'work_location_id' => ['nullable', 'exists:hr_work_locations,id'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', 'in:active,inactive,on_leave,terminated'],
            'address' => ['nullable', 'string'],
        ]);



        HrEmployee::create($data);

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function show(HrEmployee $employee)
    {
        $employee->load(['department', 'position']);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(HrEmployee $employee)
    {
        $departments = HrDepartment::orderBy('name_ar')->get();
        $positions = HrPosition::orderBy('title_ar')->get();
        $workLocations = HrWorkLocation::where('is_active', true)->orderBy('name')->get();

        return view('hr.employees.edit', compact(
            'employee',
            'departments',
            'positions',
            'workLocations'
        ));
    }

    public function update(Request $request, HrEmployee $employee)
    {
        $data = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', 'unique:hr_employees,employee_number,' . $employee->id],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:hr_employees,email,' . $employee->id],
            'mobile' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'iqama_number' => ['nullable', 'string', 'max:50'],
            'department_id' => ['nullable', 'exists:hr_departments,id'],
            'position_id' => ['nullable', 'exists:hr_positions,id'],
            'work_location_id' => ['nullable', 'exists:hr_work_locations,id'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'hire_date' => ['nullable', 'date'],
            'employment_status' => ['required', 'in:active,inactive,on_leave,terminated'],
            'address' => ['nullable', 'string'],
        ]);

$employee->update($data);

        $employee->update($data);

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(HrEmployee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('hr.employees.index')
            ->with('success', 'تم حذف الموظف بنجاح');
    }
}