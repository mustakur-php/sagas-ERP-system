<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Station;
use App\Models\Sale;
use App\Models\DailyClosing;
use Carbon\Carbon;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;



class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $companyId = session('company_id');
        $period = $request->period ?? 'daily';

        $stationsQuery = Station::query();
        $salesQuery = Sale::query();
        $closingsQuery = DailyClosing::query();

        if ($companyId) {
            $stationsQuery->where('company_id', $companyId);

            $salesQuery->whereHas('station', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

            $closingsQuery->whereHas('station', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        $stats = [
            'companies_count' => Company::count(),
            'stations_count' => $stationsQuery->count(),
            'sales_total' => $closingsQuery->sum('total_sales'),
            'closings_count' => $closingsQuery->count(),
            'collections_total' => $closingsQuery->sum('total_collections'),
            'expenses_total' => $closingsQuery->sum('total_expenses'),
        ];

        $latestClosings = DailyClosing::with('station')
            ->when($companyId, function ($q) use ($companyId) {
                $q->whereHas('station', function ($stationQuery) use ($companyId) {
                    $stationQuery->where('company_id', $companyId);
                });
            })
            ->latest()
            ->take(5)
            ->get();

        $chartQuery = DB::table('daily_closing_fuel_summaries as s')
            ->join('daily_closings as dc', 'dc.id', '=', 's.daily_closing_id')
            ->join('fuel_types as f', 'f.id', '=', 's.fuel_type_id');

        if ($companyId) {
            $chartQuery->join('stations as st', 'st.id', '=', 'dc.station_id')
                ->where('st.company_id', $companyId);
        }

        if ($period === 'monthly') {
            $chartQuery->selectRaw("
                DATE_FORMAT(dc.closing_date, '%Y-%m') as chart_date,
                f.name as fuel_name,
                SUM(s.net_liters) as total_liters
            ")->groupBy('chart_date', 'fuel_name');
        } elseif ($period === 'weekly') {
            $chartQuery->selectRaw("
                YEARWEEK(dc.closing_date, 1) as chart_date,
                f.name as fuel_name,
                SUM(s.net_liters) as total_liters
            ")->groupBy('chart_date', 'fuel_name');
        } else {
            $chartQuery->selectRaw("
                DATE(dc.closing_date) as chart_date,
                f.name as fuel_name,
                SUM(s.net_liters) as total_liters
            ")->groupBy('chart_date', 'fuel_name');
        }

        $salesData = $chartQuery->orderBy('chart_date')->get();

        $dates = $salesData->pluck('chart_date')->unique()->values();
        $fuelTypes = $salesData->pluck('fuel_name')->unique();

        $chartData = [];

        foreach ($fuelTypes as $fuel) {
            $data = [];

            foreach ($dates as $date) {
                $value = $salesData
                    ->where('fuel_name', $fuel)
                    ->where('chart_date', $date)
                    ->sum('total_liters');

                $data[] = (float) $value;
            }

            $chartData[] = [
                'label' => $fuel,
                'data' => $data,
            ];
        }

        // =========================
        // شارت المخزون
        // =========================
        $stockQuery = DB::table('stock_movements as sm')
            ->join('fuel_types as f', 'f.id', '=', 'sm.fuel_type_id');

        if ($companyId) {
            $stockQuery->join('stations as st', 'st.id', '=', 'sm.station_id')
                ->where('st.company_id', $companyId);
        }

        if ($period === 'monthly') {
            $stockQuery->selectRaw("
                DATE_FORMAT(sm.movement_date, '%Y-%m') as stock_date,
                f.name as fuel_name,
                SUM(
                    CASE
                        WHEN sm.movement_type IN ('opening_balance', 'purchase', 'adjustment')
                            THEN sm.quantity
                        WHEN sm.movement_type = 'sale'
                            THEN -sm.quantity
                        ELSE 0
                    END
                ) as stock_change
            ")->groupBy('stock_date', 'fuel_name');
        } elseif ($period === 'weekly') {
            $stockQuery->selectRaw("
                YEARWEEK(sm.movement_date, 1) as stock_date,
                f.name as fuel_name,
                SUM(
                    CASE
                        WHEN sm.movement_type IN ('opening_balance', 'purchase', 'adjustment')
                            THEN sm.quantity
                        WHEN sm.movement_type = 'sale'
                            THEN -sm.quantity
                        ELSE 0
                    END
                ) as stock_change
            ")->groupBy('stock_date', 'fuel_name');
        } else {
            $stockQuery->selectRaw("
                DATE(sm.movement_date) as stock_date,
                f.name as fuel_name,
                SUM(
                    CASE
                        WHEN sm.movement_type IN ('opening_balance', 'purchase', 'adjustment')
                            THEN sm.quantity
                        WHEN sm.movement_type = 'sale'
                            THEN -sm.quantity
                        ELSE 0
                    END
                ) as stock_change
            ")->groupBy('stock_date', 'fuel_name');
        }

        $stockRawData = $stockQuery->orderBy('stock_date')->get();

        $stockDates = $stockRawData->pluck('stock_date')->unique()->values();
        $stockFuelTypes = $stockRawData->pluck('fuel_name')->unique();

        $stockChartData = [];

        foreach ($stockFuelTypes as $fuel) {
            $runningBalance = 0;
            $data = [];

            foreach ($stockDates as $date) {
                $change = (float) $stockRawData
                    ->where('fuel_name', $fuel)
                    ->where('stock_date', $date)
                    ->sum('stock_change');

                $runningBalance += $change;
                $data[] = $runningBalance;
            }

            $stockChartData[] = [
                'label' => $fuel,
                'data' => $data,
            ];
        }

        return view('dashboard', compact(
            'stats',
            'latestClosings',
            'dates',
            'chartData',
            'stockDates',
            'stockChartData',
            'period'
        ));
    }
}