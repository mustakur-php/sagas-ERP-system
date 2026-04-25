@extends('layouts.app')

@section('content')
<div class="page-actions">
    <h2 style="margin:0;">طلبات الوقود</h2>

</div>

@if(session('success'))
    <div class="alert-success" style="padding:12px 16px; border-radius:10px; margin-bottom:16px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert-danger" style="padding:12px 16px; border-radius:10px; margin-bottom:16px; background:#fee2e2; color:#991b1b;">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>المحطة</th>
                    <th>الحالة</th>
                    <th>تاريخ الطلب</th>
                    <th>المنشئ</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->station->name ?? '-' }}</td>
                        <td>
                            @if($order->status === 'pending')
                                <span class="badge" style="background:#f59e0b;">بانتظار المراجعة</span>
                            @elseif($order->status === 'approved')
                                <span class="badge" style="background:#2563eb;">معتمد</span>
                            @elseif($order->status === 'rejected')
                                <span class="badge" style="background:#dc2626;">مرفوض</span>
                            @elseif($order->status === 'received')
                                <span class="badge" style="background:#16a34a;">تم الاستلام</span>
                            @elseif($order->status === 'cancelled')
                                <span class="badge" style="background:#6b7280;">ملغي</span>
                            @else
                                <span class="badge" style="background:#6b7280;">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td>{{ optional($order->request_date)->format('Y-m-d') ?? $order->created_at->format('Y-m-d') }}</td>
                        <td>{{ $order->creator->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('fuel-orders.show', $order->id) }}" class="btn btn-primary" style="padding:8px 12px; font-size:13px;">
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;">لا توجد طلبات حتى الآن</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection