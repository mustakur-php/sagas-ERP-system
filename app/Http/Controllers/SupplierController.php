<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use App\Models\Supplier;
use App\Models\SupplierFuelPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::with('fuelPrices.fuelType')->latest()->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $fuelTypes = FuelType::orderBy('name')->get();
        return view('suppliers.create', compact('fuelTypes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'prices' => ['nullable', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $data) {
            $supplier = Supplier::create([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            foreach (($data['prices'] ?? []) as $fuelTypeId => $price) {
                if ($price === null || $price === '') {
                    continue;
                }

                SupplierFuelPrice::updateOrCreate(
                    [
                        'supplier_id' => $supplier->id,
                        'fuel_type_id' => $fuelTypeId,
                    ],
                    [
                        'price_per_liter' => $price,
                    ]
                );
            }
        });

        return redirect()->route('suppliers.index')->with('success', 'تم إضافة المورد وأسعاره بنجاح');
    }

    public function edit(Supplier $supplier)
    {
        $supplier->load('fuelPrices');
        $fuelTypes = FuelType::orderBy('name')->get();
        $prices = $supplier->fuelPrices->keyBy('fuel_type_id');

        return view('suppliers.edit', compact('supplier', 'fuelTypes', 'prices'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'prices' => ['nullable', 'array'],
            'prices.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $supplier, $data) {
            $supplier->update([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach (($data['prices'] ?? []) as $fuelTypeId => $price) {
                if ($price === null || $price === '') {
                    SupplierFuelPrice::where('supplier_id', $supplier->id)
                        ->where('fuel_type_id', $fuelTypeId)
                        ->delete();
                    continue;
                }

                SupplierFuelPrice::updateOrCreate(
                    [
                        'supplier_id' => $supplier->id,
                        'fuel_type_id' => $fuelTypeId,
                    ],
                    [
                        'price_per_liter' => $price,
                    ]
                );
            }
        });

        return redirect()->route('suppliers.index')->with('success', 'تم تحديث المورد وأسعاره بنجاح');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد بنجاح');
    }
}
