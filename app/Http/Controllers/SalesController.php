<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Station;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $query = Sale::with('station');

        // فلترة حسب الشركة
        if (session('company_id')) {
            $query->whereHas('station', function ($q) {
                $q->where('company_id', session('company_id'));
            });
        }

        $sales = $query->latest()->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $stations = Station::when(session('company_id'), function ($q) {
            $q->where('company_id', session('company_id'));
        })->get();

        return view('sales.create', compact('stations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'amount' => 'required|numeric',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Sale::create($request->all());

        return redirect()->route('sales.index')
            ->with('success', 'تم تسجيل المبيعات');
    }

    public function edit(Sale $sale)
    {
        $stations = Station::all();

        return view('sales.edit', compact('sale', 'stations'));
    }

    public function update(Request $request, Sale $sale)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'amount' => 'required|numeric',
            'sale_date' => 'required|date',
        ]);

        $sale->update($request->all());

        return redirect()->route('sales.index')
            ->with('success', 'تم التحديث');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'تم الحذف');
    }
}