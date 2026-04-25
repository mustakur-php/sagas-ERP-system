<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Sale;
use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\FuelType;
use App\Models\StationFuelPrice;
use App\Models\StationNozzle;
use App\Models\StationTank;
use App\Models\StockMovement;





class StationController extends Controller
{
    public function index()
    {
        $query = Station::with('company');

        if (session('company_id')) {
            $query->where('company_id', session('company_id'));
        }

        $stations = $query->latest()->get();

        return view('stations.index', compact('stations'));
    }

    public function stationsDashboard()
    {
        $query = \App\Models\Station::with([
            'tanks.fuelType',
            'company',
        ]);

        if (session('company_id')) {
            $query->where('company_id', session('company_id'));
        }

        $stations = $query->with([
            'dailyClosings.fuelSummaries.fuelType'
        ])->get();

        $today = now()->toDateString();

        $criticalStationsCount = 0;
        $warningStationsCount = 0;

        foreach ($stations as $station) {
            $stationStatus = 'good';

            foreach ($station->tanks as $tank) {
                if ($tank->level_status === 'critical') {
                    $stationStatus = 'critical';
                    break;
                }

                if ($tank->level_status === 'warning') {
                    $stationStatus = 'warning';
                }
            }

            $station->dashboard_status = $stationStatus;

            if ($stationStatus === 'critical') {
                $criticalStationsCount++;
            } elseif ($stationStatus === 'warning') {
                $warningStationsCount++;
            }

            $todayClosing = $station->dailyClosings
                ->firstWhere('closing_date', $today);

            if (!$todayClosing) {
                $station->closing_status = 'not_done';
                $station->today_sales = [];
            } else {
                $station->closing_status = 'done';

                $station->today_sales = $todayClosing->fuelSummaries->map(function ($item) {
                    return [
                        'fuel' => $item->fuelType->name ?? '-',
                        'liters' => (float) $item->net_liters,
                        'amount' => (float) $item->net_amount,
                    ];
                })->values()->all();
            }
        }

        return view('dashboards.stations', compact(
            'stations',
            'criticalStationsCount',
            'warningStationsCount'
        ));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        return view('stations.create', compact('companies', 'fuelTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'code' => 'required|unique:stations,code',
            'name' => 'required',
            'name_en' => 'nullable',
            'region' => 'nullable',
            'city' => 'nullable',
            'status' => 'required',

            'fuel_prices' => 'nullable|array',
            
            'tanks' => 'nullable|array',
            'tanks.*.fuel_type_id' => 'nullable|exists:fuel_types,id',
            'tanks.*.name' => 'nullable|string|max:255',
            'tanks.*.capacity' => 'nullable|numeric|min:0',
            'tanks.*.current_quantity' => 'nullable|numeric|min:0',
            'tanks.*.warning_level' => 'nullable|numeric|min:0|max:100',
            'tanks.*.critical_level' => 'nullable|numeric|min:0|max:100',
            'nozzles' => 'nullable|array',
            'nozzles.*.pump_number' => 'nullable|integer|min:1',
            'nozzles.*.nozzle_number' => 'nullable|integer|min:1',
            'nozzles.*.fuel_type_id' => 'nullable|exists:fuel_types,id',
            'nozzles.*.last_meter_reading' => 'nullable|numeric|min:0',
            
        ]);

        DB::beginTransaction();

        try {
            $station = Station::create([
                'company_id' => $request->company_id,
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'region' => $request->region,
                'city' => $request->city,
                'status' => $request->status,
            ]);

            if ($request->has('tanks')) {
                foreach ($request->tanks as $tank) {

                    if (empty($tank['fuel_type_id']) || empty($tank['capacity'])) {
                        continue;
                    }

                    $openingBalance = (float) ($tank['current_quantity'] ?? 0);

                    $createdTank = $station->tanks()->create([
                        'fuel_type_id'     => $tank['fuel_type_id'],
                        'name'             => $tank['name'] ?? null,
                        'capacity'         => $tank['capacity'],
                        'opening_balance'  => $openingBalance,
                        'current_quantity' => $openingBalance,
                        'warning_level'    => $tank['warning_level'] ?? 30,
                        'critical_level'   => $tank['critical_level'] ?? 10,
                    ]);

                    // تسجيل حركة افتتاحية إذا كانت الكمية أكبر من صفر
                    if ($openingBalance > 0) {
                        StockMovement::create([
                            'station_tank_id' => $createdTank->id,
                            'station_id'      => $station->id,
                            'fuel_type_id'    => $tank['fuel_type_id'],
                            'movement_type'   => 'opening_balance',
                            'quantity'        => $openingBalance,
                            'movement_date'   => now()->toDateString(),
                            'reference_type'  => 'Station',
                            'reference_id'    => $station->id,
                            'notes'           => 'رصيد افتتاحي عند إنشاء المحطة',
                        ]);
                    }
                }
            }

            // حفظ أسعار الوقود
            foreach ($request->fuel_prices ?? [] as $fuelTypeId => $price) {
                if ($price !== null && $price !== '') {
                    StationFuelPrice::create([
                        'station_id' => $station->id,
                        'fuel_type_id' => $fuelTypeId,
                        'price_per_liter' => $price,
                    ]);
                }
            }

            // حفظ الليات
            foreach ($request->nozzles ?? [] as $nozzle) {
                if (
                    !empty($nozzle['pump_number']) &&
                    !empty($nozzle['nozzle_number']) &&
                    !empty($nozzle['fuel_type_id'])
                ) {
                    StationNozzle::create([
                        'station_id' => $station->id,
                        'fuel_type_id' => $nozzle['fuel_type_id'],
                        'pump_number' => $nozzle['pump_number'],
                        'nozzle_number' => $nozzle['nozzle_number'],
                        'name' => 'مضخة ' . $nozzle['pump_number'] . ' - لي ' . $nozzle['nozzle_number'],
                        'last_meter_reading' => $nozzle['last_meter_reading'] ?? 0,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('stations.index')->with('success', 'تمت إضافة المحطة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ المحطة: ' . $e->getMessage());
        }
    }
        
    public function edit(Station $station)
    {
        $companies = Company::orderBy('name')->get();
        $fuelTypes = FuelType::orderBy('name')->get();

        $station->load([
            'fuelPrices',
            'tanks',
            'nozzles' => function ($q) {
                $q->orderBy('pump_number')->orderBy('nozzle_number');
            }
        ]);

        return view('stations.edit', compact('station', 'companies', 'fuelTypes'));
    }

    public function update(Request $request, Station $station)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'code' => 'required|unique:stations,code,' . $station->id,
            'name' => 'required',
            'name_en' => 'nullable',
            'region' => 'nullable',
            'city' => 'nullable',
            'status' => 'required',

            'fuel_prices' => 'nullable|array',

            'nozzles' => 'nullable|array',
            'nozzles.*.pump_number' => 'nullable|integer|min:1',
            'nozzles.*.nozzle_number' => 'nullable|integer|min:1',
            'nozzles.*.fuel_type_id' => 'nullable|exists:fuel_types,id',
            'nozzles.*.last_meter_reading' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $station->update([
                'company_id' => $request->company_id,
                'code' => $request->code,
                'name' => $request->name,
                'name_en' => $request->name_en,
                'region' => $request->region,
                'city' => $request->city,
                'status' => $request->status,
            ]);

            if ($request->has('tanks')) {
                foreach ($request->tanks as $tankData) {

                    if (empty($tankData['fuel_type_id']) || empty($tankData['capacity'])) {
                        continue;
                    }

                    // نبحث هل فيه خزان لنفس نوع الوقود
                    $existingTank = $station->tanks()
                        ->where('fuel_type_id', $tankData['fuel_type_id'])
                        ->first();

                    if ($existingTank) {
                        // تحديث فقط (بدون تغيير الكمية)
                        $existingTank->update([
                            'name'           => $tankData['name'] ?? $existingTank->name,
                            'capacity'       => $tankData['capacity'],
                            'warning_level'  => $tankData['warning_level'] ?? $existingTank->warning_level,
                            'critical_level' => $tankData['critical_level'] ?? $existingTank->critical_level,
                        ]);
                    } else {
                        // إنشاء خزان جديد
                        $openingBalance = (float) ($tankData['current_quantity'] ?? 0);

                        $newTank = $station->tanks()->create([
                            'fuel_type_id'     => $tankData['fuel_type_id'],
                            'name'             => $tankData['name'] ?? null,
                            'capacity'         => $tankData['capacity'],
                            'opening_balance'  => $openingBalance,
                            'current_quantity' => $openingBalance,
                            'warning_level'    => $tankData['warning_level'] ?? 30,
                            'critical_level'   => $tankData['critical_level'] ?? 10,
                        ]);

                        // تسجيل حركة افتتاحية
                        if ($openingBalance > 0) {
                            \App\Models\StockMovement::create([
                                'station_tank_id' => $newTank->id,
                                'station_id'      => $station->id,
                                'fuel_type_id'    => $tankData['fuel_type_id'],
                                'movement_type'   => 'opening_balance',
                                'quantity'        => $openingBalance,
                                'movement_date'   => now()->toDateString(),
                                'reference_type'  => 'Station',
                                'reference_id'    => $station->id,
                                'notes'           => 'رصيد افتتاحي (إضافة خزان جديد)',
                            ]);
                        }
                    }
                }
            }

            // إعادة حفظ أسعار الوقود
            if ($request->has('fuel_prices')) {
                foreach ($request->fuel_prices as $fuelTypeId => $price) {
                    StationFuelPrice::updateOrCreate(
                        [
                            'station_id' => $station->id,
                            'fuel_type_id' => $fuelTypeId,
                        ],
                        [
                            'price_per_liter' => $price ?? 0,
                        ]
                    );
                }
            }

            // إعادة حفظ الليات
            $existingIds = [];

            foreach ($request->nozzles ?? [] as $nozzleData) {

                if (
                    empty($nozzleData['pump_number']) ||
                    empty($nozzleData['nozzle_number']) ||
                    empty($nozzleData['fuel_type_id'])
                ) {
                    continue;
                }

                if (!empty($nozzleData['id'])) {

                    // تحديث
                    $nozzle = StationNozzle::where('station_id', $station->id)
                        ->find($nozzleData['id']);
                        
                    if ($nozzle) {
                        $nozzle->update([
                            'fuel_type_id' => $nozzleData['fuel_type_id'],
                            'pump_number' => $nozzleData['pump_number'],
                            'nozzle_number' => $nozzleData['nozzle_number'],
                            'name' => $nozzleData['name'] ?? ('مضخة ' . $nozzleData['pump_number'] . ' - لي ' . $nozzleData['nozzle_number']),
                            'last_meter_reading' => $nozzleData['last_meter_reading'] ?? $nozzle->last_meter_reading,
                            'status' => $nozzleData['status'] ?? 'active',
                            'notes' => $nozzleData['notes'] ?? $nozzle->notes,
                        ]);

                        $existingIds[] = $nozzle->id;
                    }

                } else {

                    // إنشاء جديد
                    $new = StationNozzle::create([
                        'station_id' => $station->id,
                        'fuel_type_id' => $nozzleData['fuel_type_id'],
                        'pump_number' => $nozzleData['pump_number'],
                        'nozzle_number' => $nozzleData['nozzle_number'],
                        'name' => $nozzleData['name'] ?? ('مضخة ' . $nozzleData['pump_number'] . ' - لي ' . $nozzleData['nozzle_number']),
                        'last_meter_reading' => $nozzleData['last_meter_reading'] ?? 0,
                        'status' => $nozzleData['status'] ?? 'active',
                        'notes' => $nozzleData['notes'] ?? null,
                    ]);

                    $existingIds[] = $new->id;
                }
            }
            // StationNozzle::where('station_id', $station->id)
            //     ->whereNotIn('id', $existingIds)
            //     ->update(['status' => 'inactive']);

            DB::commit();

            return redirect()->route('stations.index')->with('success', 'تم تعديل المحطة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'حدث خطأ أثناء تعديل المحطة: ' . $e->getMessage());
        }
    }

    public function changeStatus(Station $station, $status)
    {
        $allowedStatuses = ['active', 'inactive', 'under_maintenance', 'stopped'];

        if (!in_array($status, $allowedStatuses)) {
            return redirect()->route('stations.index')->with('success', 'الحالة غير صحيحة');
        }

        $station->update([
            'status' => $status,
        ]);

        return redirect()->route('stations.index')->with('success', 'تم تحديث حالة المحطة بنجاح');
    }

}