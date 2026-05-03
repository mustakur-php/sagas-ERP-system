@extends('layouts.app')

@section('title', 'لوحة التحكم')
@section('page_title', 'الصفحة الرئيسية')
@section('page_subtitle', 'لوحة النظام الرئيسيه')

@section('content')

<div class="page-actions">
    <div class="muted">
        ملخص النظام.
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('stations.index') }}" class="btn btn-primary">المحطات</a>
        <a href="{{ route('daily_closings.index') }}" class="btn btn-secondary">الإغلاقات</a>
        <a href="{{ route('sales.index') }}" class="btn btn-info">المبيعات</a>
        <a href="{{ route('companies.index') }}" class="btn btn-success">الشركات</a>
    </div>
</div>

<div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:16px; margin-bottom:20px;">
    <div class="card">
        <div class="muted">عدد الشركات</div>
        <h2 style="margin:10px 0 0;">{{ $stats['companies_count'] }}</h2>
    </div>

    <div class="card">
        <div class="muted">عدد المحطات</div>
        <h2 style="margin:10px 0 0;">{{ $stats['stations_count'] }}</h2>
    </div>

    <div class="card">
        <div class="muted">إجمالي المبيعات</div>
        <h2 style="margin:10px 0 0;">{{ number_format($stats['sales_total'], 2) }}</h2>
    </div>

    <div class="card">
        <div class="muted">عدد الإغلاقات</div>
        <h2 style="margin:10px 0 0;">{{ $stats['closings_count'] }}</h2>
    </div>

    <div class="card">
        <div class="muted">إجمالي التحصيلات</div>
        <h2 style="margin:10px 0 0;">{{ number_format($stats['collections_total'], 2) }}</h2>
    </div>

    <div class="card">
        <div class="muted">إجمالي المصروفات</div>
        <h2 style="margin:10px 0 0;">{{ number_format($stats['expenses_total'], 2) }}</h2>
    </div>
</div>
<form method="GET" style="margin-bottom:20px;">
    <select name="period" onchange="this.form.submit()">
        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>يومي</option>
        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>أسبوعي</option>
        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>شهري</option>
    </select>
</form>
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap:20px; margin-top:20px;">

    <!-- شارت المبيعات -->
    <div class="card" style="padding:15px;">
        <h4 style="margin-top:0;">مبيعات الوقود</h4>
        <div style="height:460px;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- شارت المخزون -->
    <div class="card" style="padding:15px;">
        <h4 style="margin-top:0;">مخزون الوقود</h4>
        <div style="height:460px;">
            <canvas id="stockChart"></canvas>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

const ctx = document.getElementById('salesChart').getContext('2d');

const rawData = @json($chartData ?? []);

const datasets = Array.isArray(rawData) ? rawData.map((item) => {
    const fuelColors = {
        '91': 'green',
        '95': 'red',
        'ديزل': 'orange',
    };

    const color = fuelColors[item.label] || 'gray';

    return {
        label: item.label,
        data: item.data,
        borderColor: color,
        backgroundColor: color,
        fill: false,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 3
    };
}) : [];

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($dates ?? []),
        datasets: datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
<script>
const stockCtx = document.getElementById('stockChart').getContext('2d');

const rawStockData = @json($stockChartData ?? []);

const stockDatasets = Array.isArray(rawStockData) ? rawStockData.map((item) => {
    const fuelColors = {
        '91': 'green',
        '95': 'red',
        'ديزل': 'orange',
    };

    const color = fuelColors[item.label] || 'gray';

    return {
        label: item.label,
        data: item.data,
        borderColor: color,
        backgroundColor: color,
        fill: false,
        tension: 0.4,
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 3
    };
}) : [];

const stockChart = new Chart(stockCtx, {
    type: 'line',
    data: {
        labels: @json($stockDates ?? []),
        datasets: stockDatasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>


<div class="card">
    <h3 style="margin-top:0;">آخر الإغلاقات اليومية</h3>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>التاريخ</th>
                    <th>المحطة</th>
                    <th>المبيعات</th>
                    <th>التحصيلات</th>
                    <th>المصروفات</th>
                    <th>الفرق</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestClosings as $closing)
                    <tr>
                        <td>{{ $closing->id }}</td>
                        <td>{{ $closing->closing_date }}</td>
                        <td>{{ $closing->station->name ?? '-' }}</td>
                        <td>{{ number_format($closing->total_sales, 2) }}</td>
                        <td>{{ number_format($closing->total_collections, 2) }}</td>
                        <td>{{ number_format($closing->total_expenses, 2) }}</td>
                        <td>
                            @if($closing->difference > 0)
                                <span style="color:#dc2626;">عجز {{ number_format($closing->difference, 2) }}</span>
                            @elseif($closing->difference < 0)
                                <span style="color:#16a34a;">زيادة {{ number_format(abs($closing->difference), 2) }}</span>
                            @else
                                <span style="color:#6b7280;">متوازن</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('daily_closings.show', $closing->id) }}" class="btn btn-primary">عرض</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">لا توجد إغلاقات حتى الآن</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection