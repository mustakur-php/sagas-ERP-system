<?php

namespace App\Http\Controllers;

use App\Models\DailyClosing;
use App\Models\DailyClosingNozzle;
use App\Models\Station;
use App\Models\StationFuelPrice;
use App\Models\StationNozzle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\DailyClosingCollection;
use App\Models\DailyClosingExpense;
use App\Models\DailyClosingFuelSummary;
use App\Models\StationTank;
use App\Models\StockMovement;





class DailyClosingController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyClosing::with('station');

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('closing_date')) {
            $query->whereDate('closing_date', $request->closing_date);
        }

        $closings = $query->latest()->paginate(10)->withQueryString();
        $stations = Station::orderBy('name')->get();

        return view('daily_closings.index', compact('closings', 'stations'));
    }

    public function create(Request $request)
    {
        $stations = Station::orderBy('name')->get();
        $selectedStationId = $request->station_id;
        $stationNozzles = collect();
        $fuelSummaries = collect();

        if ($selectedStationId) {
            $stationNozzles = StationNozzle::with('fuelType')
                ->where('station_id', $selectedStationId)
                ->where('status', 'active')
                ->orderBy('pump_number')
                ->orderBy('nozzle_number')
                ->get();

            $prices = StationFuelPrice::where('station_id', $selectedStationId)
                ->get()
                ->keyBy('fuel_type_id');

            foreach ($stationNozzles as $nozzle) {
                $nozzle->sale_price = (float) ($prices[$nozzle->fuel_type_id]->price_per_liter ?? 0);
            }

            // تجهيز أنواع الوقود الفريدة للمحطة لاستخدامها في "الكميات المعادة"
            $fuelSummaries = $stationNozzles
                ->filter(fn ($nozzle) => $nozzle->fuelType)
                ->groupBy('fuel_type_id')
                ->map(function ($group) use ($prices) {
                    $first = $group->first();

                    return [
                        'fuel_type_id'   => $first->fuel_type_id,
                        'fuel_type_name' => $first->fuelType->name ?? 'غير معروف',
                        'price_per_liter'=> (float) ($prices[$first->fuel_type_id]->price_per_liter ?? 0),
                    ];
                })
                ->values();
        }

        return view('daily_closings.create', compact(
            'stations',
            'stationNozzles',
            'selectedStationId',
            'fuelSummaries'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'closing_date' => [
                'required',
                'date',
                Rule::unique('daily_closings')->where(function ($query) use ($request) {
                    return $query->where('station_id', $request->station_id);
                }),
            ],

            'station_id' => 'required|exists:stations,id',
            'notes' => 'nullable|string',

            'nozzles' => 'required|array|min:1',
            'nozzles.*.station_nozzle_id' => 'required|exists:station_nozzles,id',
            'nozzles.*.start_reading' => 'required|numeric|min:0',
            'nozzles.*.end_reading' => 'required|numeric|min:0',

            'returned' => 'nullable|array',
            'returned.*' => 'nullable|numeric|min:0',

            'collections' => 'nullable|array',
            'collections.*.collection_type' => 'nullable|string',
            'collections.*.provider_name' => 'nullable|string',
            'collections.*.amount' => 'nullable|numeric|min:0',
            'collections.*.notes' => 'nullable|string',

            'expenses' => 'nullable|array',
            'expenses.*.expense_type' => 'nullable|string',
            'expenses.*.description' => 'nullable|string',
            'expenses.*.amount' => 'nullable|numeric|min:0',
            'expenses.*.notes' => 'nullable|string',
        ], [
            'closing_date.unique' => 'يوجد إغلاق يومي مسجل لهذه المحطة في هذا التاريخ.',
            'nozzles.required' => 'لازم تدخل قراءات المسدسات.',
        ]);

        DB::transaction(function () use ($request) {
            $totalCollections = 0;
            $totalExpenses = 0;
            $closing = DailyClosing::create([
                'station_id' => $request->station_id,
                'closing_date' => $request->closing_date,
                'total_sales' => 0,
                'total_collections' => 0,
                'total_expenses' => 0,
                'notes' => $request->notes,
            ]);

            $grandAmount = 0;

            foreach ($request->nozzles as $row) {
                $stationNozzle = StationNozzle::with('fuelType')
                    ->where('station_id', $request->station_id)
                    ->findOrFail($row['station_nozzle_id']);

                $stationFuelPrice = StationFuelPrice::where('station_id', $request->station_id)
                    ->where('fuel_type_id', $stationNozzle->fuel_type_id)
                    ->first();

                $start = (float) $row['start_reading'];
                $end = (float) $row['end_reading'];

                if ($end < $start) {
                    abort(422, "قراءة النهاية لا يمكن تكون أقل من البداية للمسدس رقم {$stationNozzle->nozzle_number} في المضخة {$stationNozzle->pump_number}.");
                }

                $liters = $end - $start;
                $price = (float) ($stationFuelPrice->price_per_liter ?? 0);
                $amount = $liters * $price;

                $grandAmount += $amount;

                DailyClosingNozzle::create([
                    'daily_closing_id' => $closing->id,
                    'station_nozzle_id' => $stationNozzle->id,
                    'start_reading' => $start,
                    'end_reading' => $end,
                    'total_liters' => $liters,
                    'price' => $price,
                    'total_amount' => $amount,
                ]);

                $stationNozzle->update([
                    'last_meter_reading' => $end,
                ]);
            }

            $fuelTotals = [];

            // تجميع اللترات حسب نوع الوقود
            foreach ($request->nozzles as $row) {

                $stationNozzle = StationNozzle::with('fuelType')
                    ->where('station_id', $request->station_id)
                    ->findOrFail($row['station_nozzle_id']);

                $start = (float) $row['start_reading'];
                $end = (float) $row['end_reading'];

                $liters = $end - $start;

                if (!isset($fuelTotals[$stationNozzle->fuel_type_id])) {
                    $fuelTotals[$stationNozzle->fuel_type_id] = 0;
                }

                $fuelTotals[$stationNozzle->fuel_type_id] += $liters;
            }


            $totalSalesAfterReturn = 0;

            foreach ($fuelTotals as $fuelTypeId => $totalLiters) {

                $returned = (float) ($request->returned[$fuelTypeId] ?? 0);
                $netLiters = max($totalLiters - $returned, 0);

                $price = (float) (StationFuelPrice::where('station_id', $request->station_id)
                    ->where('fuel_type_id', $fuelTypeId)
                    ->value('price_per_liter') ?? 0);

                $netAmount = $netLiters * $price;

                // 1) حفظ ملخص الوقود
                DailyClosingFuelSummary::create([
                    'daily_closing_id' => $closing->id,
                    'fuel_type_id' => $fuelTypeId,
                    'total_liters' => $totalLiters,
                    'returned_liters' => $returned,
                    'net_liters' => $netLiters,
                    'price_per_liter' => $price,
                    'net_amount' => $netAmount,
                ]);

                // 2) جلب الخزان الخاص بالمحطة ونوع الوقود
                $tank = StationTank::where('station_id', $request->station_id)
                    ->where('fuel_type_id', $fuelTypeId)
                    ->first();

                if ($tank) {

                    // 3) حركة البيع - تنقص من الخزان
                    if ($netLiters > 0) {
                        StockMovement::create([
                            'station_tank_id' => $tank->id,
                            'station_id' => $tank->station_id,
                            'fuel_type_id' => $fuelTypeId,
                            'movement_type' => 'sale',
                            'quantity' => $netLiters,
                            'movement_date' => $request->closing_date,
                            'reference_type' => 'DailyClosing',
                            'reference_id' => $closing->id,
                            'notes' => 'حركة بيع من الإغلاق اليومي',
                        ]);

                        $tank->current_quantity = $tank->current_quantity - $netLiters;
                    }

                    // 5) حماية من النزول تحت الصفر
                    $tank->current_quantity = max((float) $tank->current_quantity, 0);

                    // 6) حفظ الخزان بعد التعديل
                    $tank->save();
                }

                $totalSalesAfterReturn += $netAmount;
            }

            // ===== التحصيلات =====
            foreach ($request->collections ?? [] as $collection) {

                if (
                    empty($collection['collection_type']) &&
                    empty($collection['provider_name']) &&
                    empty($collection['amount']) &&
                    empty($collection['notes'])
                ) {
                    continue;
                }

                if (empty($collection['collection_type']) || empty($collection['amount'])) {
                    continue;
                }

                DailyClosingCollection::create([
                    'daily_closing_id' => $closing->id,
                    'collection_type' => $collection['collection_type'],
                    'provider_name' => $collection['provider_name'] ?? null,
                    'amount' => $collection['amount'],
                    'notes' => $collection['notes'] ?? null,
                ]);

                $totalCollections += (float) $collection['amount'];
            }

            // ===== المصروفات =====
            foreach ($request->expenses ?? [] as $expense) {

                if (
                    empty($expense['expense_type']) &&
                    empty($expense['description']) &&
                    empty($expense['amount']) &&
                    empty($expense['notes'])
                ) {
                    continue;
                }

                if (empty($expense['expense_type']) || empty($expense['amount'])) {
                    continue;
                }

                DailyClosingExpense::create([
                    'daily_closing_id' => $closing->id,
                    'expense_type' => $expense['expense_type'],
                    'description' => $expense['description'] ?? null,
                    'amount' => $expense['amount'],
                    'notes' => $expense['notes'] ?? null,
                ]);

                $totalExpenses += (float) $expense['amount'];
            }

            // ===== تحديث الإغلاق =====
            $closing->update([
                'total_sales' => $totalSalesAfterReturn,
                'total_collections' => $totalCollections,
                'total_expenses' => $totalExpenses,
            ]);
        });

        return redirect()
            ->route('daily_closings.index')
            ->with('success', 'تم حفظ الإغلاق اليومي بنجاح.');
    }

    public function show($id)
    {
        $closing = DailyClosing::with([
            'station',
            'collections',
            'expenses',
            'nozzleReadings.stationNozzle.fuelType',
            'fuelSummaries.fuelType',
        ])->findOrFail($id);

        return view('daily_closings.show', compact('closing'));
    }
}