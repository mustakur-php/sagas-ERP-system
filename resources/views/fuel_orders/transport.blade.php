@extends('layouts.app')

@section('content')
<div class="container">
    <h2>طلبات بانتظار النقل</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>رقم الطلب</th>
                <th>المحطة</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->station->name }}</td>
                    <td>
                        <a href="{{ route('fuel-orders.transport.edit', $order->id) }}" class="btn btn-primary">
                            تجهيز
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection