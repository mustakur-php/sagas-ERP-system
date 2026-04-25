@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="margin-bottom: 20px;">إضافة إغلاق يومي</h1>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 15px;">
            <ul style="margin:0; padding-right:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('daily_closings.create') }}" style="margin-bottom: 20px;">
        <div style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
            <div>
                <label>المحطة</label><br>
                <select name="station_id" class="form-control" required>
                    <option value="">-- اختر المحطة --</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->id }}" {{ (string)$selectedStationId === (string)$station->id ? 'selected' : '' }}>
                            {{ $station->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">عرض المسدسات</button>
            </div>
        </div>
    </form>

    @if($selectedStationId)
        <form method="POST" action="{{ route('daily_closings.store') }}">
            @csrf

            <input type="hidden" name="station_id" value="{{ $selectedStationId }}">

            <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:20px;">
                <div>
                    <label>تاريخ الإغلاق</label><br>
                    <input type="date" name="closing_date" class="form-control" value="{{ old('closing_date', date('Y-m-d')) }}" required>
                </div>

                <div style="min-width:300px;">
                    <label>ملاحظات</label><br>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}">
                </div>
            </div>

            @if($stationNozzles->count())
                <div style="overflow-x:auto;">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>المضخة</th>
                                <th>المسدس</th>
                                <th>النوع</th>
                                <th>السعر</th>
                                <th>آخر قراءة</th>
                                <th>بداية القراءة</th>
                                <th>نهاية القراءة</th>
                                <th>اللترات</th>
                                <th>القيمة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stationNozzles as $index => $nozzle)
                                <tr data-fuel-type-id="{{ $nozzle->fuel_type_id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $nozzle->pump_number }}</td>
                                    <td>{{ $nozzle->nozzle_number }}</td>
                                    <td>{{ $nozzle->fuelType->name ?? '-' }}</td>
                                    <td>
                                        <span class="fuel-price" data-price="{{ $nozzle->sale_price ?? 0 }}">
                                            {{ number_format($nozzle->sale_price ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($nozzle->last_meter_reading, 2) }}</td>
                                    <td>
                                        <input type="hidden" name="nozzles[{{ $index }}][station_nozzle_id]" value="{{ $nozzle->id }}">
                                        <input
                                            type="number"
                                            name="nozzles[{{ $index }}][start_reading]"
                                            class="form-control start-reading"
                                            step="0.01"
                                            min="0"
                                            value="{{ old("nozzles.$index.start_reading", $nozzle->last_meter_reading) }}"
                                            required
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="nozzles[{{ $index }}][end_reading]"
                                            class="form-control end-reading"
                                            step="0.01"
                                            min="0"
                                            value="{{ old("nozzles.$index.end_reading") }}"
                                            required
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control total-liters"
                                            value="0.00"
                                            readonly
                                        >
                                    </td>
                                    <td>
                                        <input
                                            type="text"
                                            class="form-control total-amount"
                                            value="0.00"
                                            readonly
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top:20px; display:flex; gap:20px;">
                        <div>
                            <label>إجمالي اللترات</label>
                            <input type="text" id="grand-total-liters" class="form-control" readonly>
                        </div>

                        <div>
                            <label>إجمالي قيمة المبيعات</label>
                            <input type="text" id="grand-total-amount" class="form-control" readonly>
                        </div>
                    </div>
                    <hr>
                    <h4>الكميات المعادة</h4>

                    @if($fuelSummaries->count())
                        <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
                            @foreach($fuelSummaries as $fuel)
                                <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap; border:1px solid #e5e7eb; padding:12px; border-radius:8px;">
                                    <div style="min-width:180px;">
                                        <label>نوع الوقود</label>
                                        <input type="text" class="form-control" value="{{ $fuel['fuel_type_name'] }}" readonly>
                                    </div>

                                    <div style="min-width:180px;">
                                        <label>اللترات المحتسبة</label>
                                        <input
                                            type="text"
                                            class="form-control fuel-total-liters"
                                            data-fuel-type-id="{{ $fuel['fuel_type_id'] }}"
                                            value="0.00"
                                            readonly
                                        >
                                    </div>

                                    <div style="min-width:180px;">
                                        <label>الكمية المعادة</label>
                                        <input
                                            type="number"
                                            name="returned[{{ $fuel['fuel_type_id'] }}]"
                                            class="form-control returned-liters"
                                            data-fuel-type-id="{{ $fuel['fuel_type_id'] }}"
                                            step="0.01"
                                            min="0"
                                            value="{{ old('returned.' . $fuel['fuel_type_id'], 0) }}"
                                        >
                                    </div>

                                    <div style="min-width:180px;">
                                        <label>صافي اللترات</label>
                                        <input
                                            type="text"
                                            class="form-control fuel-net-liters"
                                            data-fuel-type-id="{{ $fuel['fuel_type_id'] }}"
                                            value="0.00"
                                            readonly
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <hr>
                    <hr>
                    <h4>التحصيلات</h4>

                    @for($i = 0; $i < 4; $i++)
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <select name="collections[{{ $i }}][collection_type]" class="form-control">
                                <option value="">نوع التحصيل</option>
                                <option value="cash">نقد</option>
                                <option value="card">شبكة</option>
                                <option value="company_account">شركة</option>
                                <option value="wallet">محفظة</option>
                            </select>

                            <input type="text" name="collections[{{ $i }}][provider_name]" placeholder="الجهة" class="form-control">

                            <input type="number" step="0.01" name="collections[{{ $i }}][amount]" placeholder="المبلغ" class="form-control">

                            <input type="text" name="collections[{{ $i }}][notes]" placeholder="ملاحظات" class="form-control">
                        </div>
                    @endfor
                    <hr>
                    <h4>المصروفات</h4>

                    @for($i = 0; $i < 4; $i++)
                        <div style="display:flex; gap:10px; margin-bottom:10px;">
                            <input type="text" name="expenses[{{ $i }}][expense_type]" placeholder="نوع المصروف" class="form-control">

                            <input type="text" name="expenses[{{ $i }}][description]" placeholder="الوصف" class="form-control">

                            <input type="number" step="0.01" name="expenses[{{ $i }}][amount]" placeholder="المبلغ" class="form-control">

                            <input type="text" name="expenses[{{ $i }}][notes]" placeholder="ملاحظات" class="form-control">
                        </div>
                    @endfor
                </div>

                <button type="submit" class="btn btn-success">حفظ الإغلاق</button>
            @else
                <div class="alert alert-warning">
                    لا توجد مسدسات نشطة لهذه المحطة.
                </div>
            @endif
        </form>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    function calculateRowTotal(row) {
        const startInput = row.querySelector('.start-reading');
        const endInput = row.querySelector('.end-reading');
        const litersInput = row.querySelector('.total-liters');
        const amountInput = row.querySelector('.total-amount');
        const priceElement = row.querySelector('.fuel-price');

        const start = parseFloat(startInput.value || 0);
        const end = parseFloat(endInput.value || 0);
        const price = parseFloat(priceElement.dataset.price || 0);

        if (!isNaN(start) && !isNaN(end) && end >= start) {
            const liters = end - start;
            const amount = liters * price;

            litersInput.value = liters.toFixed(2);
            amountInput.value = amount.toFixed(2);
        } else {
            litersInput.value = '0.00';
            amountInput.value = '0.00';
        }

        calculateGrandTotals();
        calculateFuelSummaries();
    }

    function calculateGrandTotals() {
        let totalLiters = 0;
        let totalAmount = 0;

        document.querySelectorAll('.total-liters').forEach(function (input) {
            totalLiters += parseFloat(input.value || 0);
        });

        document.querySelectorAll('.total-amount').forEach(function (input) {
            totalAmount += parseFloat(input.value || 0);
        });

        const grandLiters = document.getElementById('grand-total-liters');
        const grandAmount = document.getElementById('grand-total-amount');

        if (grandLiters) grandLiters.value = totalLiters.toFixed(2);
        if (grandAmount) grandAmount.value = totalAmount.toFixed(2);
    }

    function calculateFuelSummaries() {
        const fuelTotals = {};

        document.querySelectorAll('tbody tr[data-fuel-type-id]').forEach(function (row) {
            const fuelTypeId = row.dataset.fuelTypeId;
            const liters = parseFloat(row.querySelector('.total-liters')?.value || 0);

            if (!fuelTotals[fuelTypeId]) {
                fuelTotals[fuelTypeId] = 0;
            }

            fuelTotals[fuelTypeId] += liters;
        });

        document.querySelectorAll('.fuel-total-liters').forEach(function (input) {
            const fuelTypeId = input.dataset.fuelTypeId;
            const total = fuelTotals[fuelTypeId] || 0;
            input.value = total.toFixed(2);
        });

        document.querySelectorAll('.fuel-net-liters').forEach(function (input) {
            const fuelTypeId = input.dataset.fuelTypeId;
            const total = fuelTotals[fuelTypeId] || 0;

            const returnedInput = document.querySelector('.returned-liters[data-fuel-type-id="' + fuelTypeId + '"]');
            const returned = parseFloat(returnedInput?.value || 0);

            const net = Math.max(total - returned, 0);
            input.value = net.toFixed(2);
        });
    }

    document.querySelectorAll('tbody tr').forEach(function (row) {
        const startInput = row.querySelector('.start-reading');
        const endInput = row.querySelector('.end-reading');

        if (startInput && endInput) {
            startInput.addEventListener('input', function () {
                calculateRowTotal(row);
            });

            endInput.addEventListener('input', function () {
                calculateRowTotal(row);
            });

            calculateRowTotal(row);
        }
    });

    document.querySelectorAll('.returned-liters').forEach(function (input) {
        input.addEventListener('input', function () {
            calculateFuelSummaries();
        });
    });

    calculateGrandTotals();
    calculateFuelSummaries();
});
</script>
@endsection