@extends('layouts.app')

@section('content')
<div class="container">
    <h2>طلبات بانتظار التشغيل</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>المحطة</th>
                <th>التاريخ</th>
                <th>عرض</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->station->name }}</td>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('fuel-orders.show', $order->id) }}" class="btn btn-primary btn-sm">
                            فتح
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection