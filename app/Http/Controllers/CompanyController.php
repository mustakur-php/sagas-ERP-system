<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::latest()->get();

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        Company::create($request->all());

        return redirect()->route('companies.index')
            ->with('success', 'تمت إضافة الشركة بنجاح');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code,' . $company->id,
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $company->update($request->all());

        return redirect()->route('companies.index')
            ->with('success', 'تم تحديث الشركة');
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'تم حذف الشركة');
    }
}