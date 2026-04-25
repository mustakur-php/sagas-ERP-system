<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers = Carrier::latest()->get();
        return view('carriers.index', compact('carriers'));
    }

    public function create()
    {
        return view('carriers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Carrier::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('carriers.index')->with('success', 'تم إضافة الناقل بنجاح');
    }

    public function edit(Carrier $carrier)
    {
        return view('carriers.edit', compact('carrier'));
    }

    public function update(Request $request, Carrier $carrier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $carrier->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('carriers.index')->with('success', 'تم تحديث الناقل بنجاح');
    }

    public function destroy(Carrier $carrier)
    {
        $carrier->delete();
        return redirect()->route('carriers.index')->with('success', 'تم حذف الناقل بنجاح');
    }
}
