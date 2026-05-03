<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\HrDepartment;
use App\Models\HrPosition;
use Illuminate\Http\Request;

class HrOrganizationController extends Controller
{
    public function index()
    {
        $departments = HrDepartment::withCount('employees')->latest()->get();
        $positions = HrPosition::with('department')->latest()->get();

        return view('hr.organization.index', compact('departments', 'positions'));
    }

    public function storeDepartment(Request $request)
    {
        $data = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        HrDepartment::create($data);

        return redirect()->route('hr.organization.index')
            ->with('success', 'تم إضافة القسم بنجاح');
    }

    public function storePosition(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required', 'exists:hr_departments,id'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
            'default_basic_salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        HrPosition::create($data);

        return redirect()->route('hr.organization.index')
            ->with('success', 'تم إضافة المنصب بنجاح');
    }
}