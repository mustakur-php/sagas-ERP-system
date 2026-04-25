@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="margin-bottom: 20px;">تفاصيل الإغلاق اليومي</h1>

    <div class="card" style="padding:15px; margin-bottom:20px;">
        <p><strong>رقم الإغلاق:</strong> {{ $closing->id }}</p>
        <p><strong>المحطة:</strong> {{ $closing->station->name ?? '-' }}</p>
        <p><strong>التاريخ:</strong> {{ $closing->closing_date }}</p>
        <p><strong>ملاحظات:</strong> {{ $closing->notes ?: '-' }}</p>
        <p><strong>إجمالي المبيعات:</strong> {{ number_format($closing->total_sales, 2) }} ريال</p>
        <p><strong>إجمالي التحصيلات:</strong> {{ number_format($closing->total_collections, 2) }} ريال</p>
        <p><strong>إجمالي المصروفات:</strong> {{ number_format($closing->total_expenses, 2) }} ريال</p>
    </div>

    <div style="overflow-x:auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المضخة</th>
                    <th>المسدس</th>
                    <th>النوع</th>
                    <th>البداية</th>
                    <th>النهاية</th>
                    <th>اللترات</th>
                    <th>السعر</th>
                    <th>القيمة</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $grandLiters = 0; 
                    $grandAmount = 0; 
                @endphp

                @foreach($closing->nozzleReadings as $index => $reading)
                    @php 
                        $grandLiters += $reading->total_liters; 
                        $grandAmount += $reading->total_amount; 
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $reading->stationNozzle->pump_number ?? '-' }}</td>
                        <td>{{ $reading->stationNozzle->nozzle_number ?? '-' }}</td>
                        <td>{{ $reading->stationNozzle->fuelType->name ?? '-' }}</td>
                        <td>{{ number_format($reading->start_reading, 2) }}</td>
                        <td>{{ number_format($reading->end_reading, 2) }}</td>
                        <td>{{ number_format($reading->total_liters, 2) }}</td>
                        <td>{{ number_format($reading->price, 2) }}</td>
                        <td>{{ number_format($reading->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" style="text-align:center;">الإجمالي</th>
                    <th>{{ number_format($grandLiters, 2) }}</th>
                    <th>-</th>
                    <th>{{ number_format($grandAmount, 2) }}</th>
                </tr>
            </tfoot>
        </table>
        <hr>

        <h4>التحصيلات</h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>الجهة</th>
                    <th>المبلغ</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($closing->collections as $collection)
                    <tr>
                        <td>{{ $collection->collection_type }}</td>
                        <td>{{ $collection->provider_name ?? '-' }}</td>
                        <td>{{ number_format($collection->amount, 2) }}</td>
                        <td>{{ $collection->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">لا توجد تحصيلات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <hr>

        <h4>ملخص الوقود</h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>نوع الوقود</th>
                    <th>اللترات الإجمالية</th>
                    <th>المعاد</th>
                    <th>الصافي</th>
                    <th>السعر</th>
                    <th>القيمة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($closing->fuelSummaries as $fuel)
                    <tr>
                        <td>{{ $fuel->fuelType->name ?? '-' }}</td>
                        <td>{{ number_format($fuel->total_liters, 2) }}</td>
                        <td>{{ number_format($fuel->returned_liters, 2) }}</td>
                        <td>{{ number_format($fuel->net_liters, 2) }}</td>
                        <td>{{ number_format($fuel->price_per_liter, 2) }}</td>
                        <td>{{ number_format($fuel->net_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">لا يوجد بيانات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <hr>

        <h4>المصروفات</h4>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>النوع</th>
                    <th>الوصف</th>
                    <th>المبلغ</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($closing->expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_type }}</td>
                        <td>{{ $expense->description ?? '-' }}</td>
                        <td>{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">لا توجد مصروفات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:15px;">
        <div>
            <label><strong>إجمالي اللترات</strong></label>
            <input type="text" class="form-control" value="{{ number_format($grandLiters, 2) }}" readonly>
        </div>

        <div>
            <label><strong>إجمالي قيمة المبيعات</strong></label>
            <input type="text" class="form-control" value="{{ number_format($grandAmount, 2) }}" readonly>
        </div>
        <hr>

        @php
            $difference = $closing->total_sales - $closing->total_collections - $closing->total_expenses;
        @endphp

        <div style="margin-top:20px;">
            <h4>الملخص النهائي</h4>

            <p>إجمالي المبيعات: {{ number_format($closing->total_sales, 2) }} ريال</p>
            <p>إجمالي التحصيلات: {{ number_format($closing->total_collections, 2) }} ريال</p>
            <p>إجمالي المصروفات: {{ number_format($closing->total_expenses, 2) }} ريال</p>

            <hr>

            <h3 style="color: {{ $difference == 0 ? 'green' : ($difference > 0 ? 'red' : 'orange') }}">
                الفرق: {{ number_format($difference, 2) }} ريال
            </h3>
        </div>
    </div>


    <a href="{{ route('daily_closings.index') }}" class="btn btn-secondary">رجوع</a>
</div>
@endsection