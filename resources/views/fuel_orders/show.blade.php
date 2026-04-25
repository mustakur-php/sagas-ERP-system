@extends('layouts.app')

<style>
    .fuel-order-page .card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .fuel-order-page .table {
        width: 100% !important;
        margin-bottom: 0;
    }

    .fuel-order-page .table th,
    .fuel-order-page .table td {
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .fuel-order-page .actions-cell {
        min-width: 470px;
        width: 470px;
        white-space: nowrap;
    }

    .fuel-order-page .actions-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: nowrap !important;
        width: 100%;
    }

    .fuel-order-page .actions-wrapper form {
        display: inline-flex !important;
        align-items: center;
        gap: 6px;
        margin: 0;
        padding: 0;
        width: auto !important;
        flex-wrap: nowrap !important;
    }

    .fuel-order-page .actions-wrapper input[type="number"] {
        width: 115px;
        height: 34px;
        font-size: 12px;
        text-align: center;
    }

    .fuel-order-page .actions-wrapper input[type="text"] {
        width: 150px;
        height: 34px;
        font-size: 12px;
        text-align: center;
    }

    .fuel-order-page .actions-wrapper .btn {
        height: 34px;
        min-width: 48px;
        white-space: nowrap;
        padding: 0 12px;
        font-size: 12px;
        line-height: 32px;
    }

    .fuel-order-page .status-badge {
        display: inline-block;
        min-width: 90px;
        padding: 6px 10px;
        font-size: 12px;
    }

    .fuel-order-page .order-info div {
        margin-bottom: 4px;
    }
</style>

@section('content')
<div class="container fuel-order-page">

    <h2 class="mb-4">تفاصيل طلب الوقود</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @php
        $orderStatusColors = [
            'submitted' => 'secondary',
            'operations_review' => 'info',
            'finance_review' => 'warning text-dark',
            'finance_availability_review' => 'warning text-dark',
            'transport_assignment' => 'primary',
            'finance_payment' => 'warning text-dark',
            'station_receipt' => 'primary',
            'completed' => 'success',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
        ];
    @endphp

    <div class="card mb-4">
        <div class="card-body order-info">
            <div><strong>رقم الطلب:</strong> {{ $order->order_number }}</div>
            <div><strong>المحطة:</strong> {{ $order->station->name ?? '-' }}</div>
            <div>
                <strong>الحالة:</strong>
                <span class="badge bg-{{ $orderStatusColors[$order->status] ?? 'secondary' }} status-badge">
                    {{ $order->status }}
                </span>
            </div>
            <div><strong>المرحلة الحالية:</strong> {{ $order->current_step ?? '-' }}</div>
            <div><strong>منشئ الطلب:</strong> {{ $order->creator->name ?? '-' }}</div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">أصناف الطلب</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>نوع الوقود</th>
                            <th>المطلوب</th>
                            <th>المعتمد</th>
                            <th>المستلم</th>
                            <th>الحالة</th>
                            <th>سبب الرفض</th>
                            <th class="actions-cell">الإجراء</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->fuelType->name ?? '-' }}</td>
                                <td>{{ $item->requested_quantity }}</td>
                                <td>{{ $item->approved_quantity ?? '-' }}</td>
                                <td>{{ $item->received_quantity ?? '-' }}</td>

                                <td>
                                    @if($item->status === 'approved' && $item->approved_quantity < $item->requested_quantity)
                                        <span class="badge bg-warning text-dark">معتمد جزئيًا</span>
                                    @elseif($item->status === 'approved')
                                        <span class="badge bg-success">معتمد</span>
                                    @elseif($item->status === 'rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                    @elseif($item->status === 'received')
                                        <span class="badge bg-primary">مستلم</span>
                                    @elseif($item->status === 'cancelled')
                                        <span class="badge bg-secondary">ملغي</span>
                                    @else
                                        <span class="badge bg-secondary">معلق</span>
                                    @endif
                                </td>

                                <td>{{ $item->rejection_reason ?? '-' }}</td>

                                <td class="actions-cell text-center">
                                    @if($order->current_step === 'operations_review' && $item->status === 'pending')
                                    <div class="actions-wrapper">
                                        <form method="POST" action="{{ route('fuel-orders.items.approve', $item->id) }}">
                                            @csrf

                                            <input type="number"
                                                name="approved_quantity"
                                                class="form-control form-control-sm"
                                                placeholder="الكمية"
                                                step="0.01"
                                                min="0"
                                                value="{{ old('approved_quantity', $item->approved_quantity ?? $item->requested_quantity) }}">

                                            <button type="submit" class="btn btn-success btn-sm">
                                                اعتماد
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('fuel-orders.items.reject', $item->id) }}">
                                            @csrf

                                            <input type="text"
                                                name="reason"
                                                class="form-control form-control-sm"
                                                placeholder="سبب الرفض"
                                                value="{{ old('reason') }}">

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                رفض
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($order->current_step === 'finance_review')
        <div class="card mb-4">
            <div class="card-header">مراجعة توفر المبلغ</div>
            <div class="card-body">
                <form method="POST" action="{{ route('fuel-orders.finance.approve', $order->id) }}" class="mb-3">
                    @csrf

                    <div class="mb-2">
                        <label>المبلغ المتوقع / المتاح</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">
                        تأكيد توفر المبلغ
                    </button>
                </form>

                <form method="POST" action="{{ route('fuel-orders.finance.reject', $order->id) }}">
                    @csrf

                    <div class="mb-2">
                        <label>سبب الإرجاع للتشغيل</label>
                        <textarea name="reason" class="form-control" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger">
                        إرجاع للتشغيل
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if($order->transport)
        <div class="card mb-4">
            <div class="card-header">بيانات النقل</div>
            <div class="card-body">
                <p><strong>المورد:</strong> {{ optional($order->transport->supplier)->name ?? '-' }}</p>
                <p><strong>الناقل:</strong> {{ optional($order->transport->carrier)->name ?? '-' }}</p>
                <p><strong>تكلفة النقل:</strong> {{ $order->transport->transport_cost ?? '-' }}</p>
                <p><strong>السائق:</strong> {{ $order->transport->driver_name ?? '-' }}</p>
                <p><strong>رقم الشاحنة:</strong> {{ $order->transport->truck_number ?? '-' }}</p>
                <p><strong>الحالة:</strong> {{ $order->transport->status ?? '-' }}</p>
            </div>
        </div>
    @endif

    @if($order->current_step === 'transport_assignment' && !$order->transport)
        <form method="POST" action="{{ route('fuel-orders.transport.assign', $order->id) }}">
            @csrf

            <div class="card mb-4">
                <div class="card-header">تعيين النقل</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label>المورد</label>
                        <select name="supplier_id" id="supplier_id" class="form-control" required>
                            <option value="">اختر المورد</option>

                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="supplier-cost-box" class="mt-3 d-none">
                        <h6 class="mb-2">تكلفة الوقود حسب سعر المورد</h6>

                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>نوع الوقود</th>
                                    <th>الكمية المطلوبة</th>
                                    <th>سعر المورد</th>
                                    <th>التكلفة</th>
                                </tr>
                            </thead>
                            <tbody id="supplier-cost-body"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3">الإجمالي</th>
                                    <th id="supplier-total-cost">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-2">
                        <label>الناقل</label>
                        <select name="carrier_id" class="form-control">
                            <option value="">-- اختياري --</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>تكلفة النقل</label>
                        <input type="number" step="0.01" name="transport_cost" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>اسم السائق</label>
                        <input type="text" name="driver_name" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>رقم الشاحنة</label>
                        <input type="text" name="truck_number" class="form-control">
                    </div>

                    <button class="btn btn-primary mt-2">
                        حفظ بيانات النقل
                    </button>
                </div>
            </div>
        </form>
    @endif

    @if($order->current_step === 'finance_payment')
        <form method="POST" action="{{ route('fuel-orders.finance.payment', $order->id) }}">
            @csrf

            <div class="card mb-4">
                <div class="card-header">تأكيد الدفع</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label>رقم الحوالة / مرجع الدفع</label>
                        <input type="text" name="payment_reference" class="form-control" required>
                    </div>

                    <button class="btn btn-success mt-2">
                        تأكيد الدفع
                    </button>
                </div>
            </div>
        </form>
    @endif

    @if($order->current_step === 'station_receipt')
        <div class="card mb-4">
            <div class="card-header">استلام الوقود</div>
            <div class="card-body">
                <a href="{{ route('fuel-orders.receiving.edit', $order->id) }}" class="btn btn-primary">
                    الانتقال إلى شاشة الاستلام
                </a>
            </div>
        </div>
    @endif

    @if($order->logs && $order->logs->count())
        <div class="card mb-4">
            <div class="card-header">سجل حركة الطلب</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>المستخدم</th>
                                <th>الإجراء</th>
                                <th>من مرحلة</th>
                                <th>إلى مرحلة</th>
                                <th>الملاحظات</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->logs as $log)
                                <tr>
                                    <td>{{ $log->user->name ?? '-' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->from_step ?? '-' }}</td>
                                    <td>{{ $log->to_step ?? '-' }}</td>
                                    <td>{{ $log->notes ?? '-' }}</td>
                                    <td>{{ $log->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
<script>
    const supplierPrices = {!! json_encode(
        $suppliers->mapWithKeys(function ($supplier) {
            return [
                $supplier->id => $supplier->fuelPrices->mapWithKeys(function ($price) {
                    return [
                        $price->fuel_type_id => $price->price_per_liter
                    ];
                })->toArray()
            ];
        })->toArray()
    ) !!};

    const orderItems = {!! json_encode(
        $order->items->map(function ($item) {
            return [
                'fuel_type_id' => $item->fuel_type_id,
                'fuel_name' => $item->fuelType->name ?? 'وقود',
                'quantity' => $item->approved_quantity ?? $item->requested_quantity,
            ];
        })->toArray()
    ) !!};

    document.getElementById('supplier_id')?.addEventListener('change', function () {
        const supplierId = String(this.value);
        const box = document.getElementById('supplier-cost-box');
        const body = document.getElementById('supplier-cost-body');
        const totalCell = document.getElementById('supplier-total-cost');

        body.innerHTML = '';
        let total = 0;

        if (!supplierId || !supplierPrices[supplierId]) {
            box.classList.add('d-none');
            totalCell.innerText = '0.00';
            return;
        }

        orderItems.forEach(item => {
            const price = parseFloat(
                supplierPrices[String(supplierId)]?.[String(item.fuel_type_id)] || 0
            );
            const quantity = parseFloat(item.quantity || 0);
            const cost = price * quantity;

            total += cost;

            body.innerHTML += `
                <tr>
                    <td>${item.fuel_name}</td>
                    <td>${quantity.toLocaleString()}</td>
                    <td>${price.toFixed(2)}</td>
                    <td>${cost.toFixed(2)}</td>
                </tr>
            `;
        });

        totalCell.innerText = total.toFixed(2);
        box.classList.remove('d-none');
    });
</script>