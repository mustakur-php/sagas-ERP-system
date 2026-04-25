@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">طلبات الوقود الجاهزة للاستلام</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>المحطة</th>
                        <th>الحالة</th>
                        <th>تاريخ الطلب</th>
                        <th>الأصناف</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->station->name ?? '-' }}</td>
                            <td>
                                @if($order->status === 'approved')
                                    <span class="badge bg-primary">معتمد</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endif
                            </td>
                            <td>{{ optional($order->request_date)->format('Y-m-d') ?? $order->created_at->format('Y-m-d') }}</td>
                            <td>
                                @foreach($order->items as $item)
                                    <div>
                                        {{ $item->fuelType->name ?? '-' }}:
                                        {{ $item->approved_quantity ?? $item->requested_quantity }}
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('fuel-orders.receiving.edit', $order->id) }}" class="btn btn-sm btn-primary">
                                    استلام
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">لا توجد طلبات جاهزة للاستلام</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection