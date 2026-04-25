@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        تأكيد استلام طلب الوقود
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('fuel-orders.receiving.update', $order->id) }}">
            @csrf

            <p>رقم الطلب: <strong>{{ $order->order_number }}</strong></p>

            @foreach($order->items as $item)
                @if($item->status === 'approved')
                    <div class="mb-3">
                        <label>
                            {{ $item->fuelType->name }}
                            - الكمية المعتمدة: {{ $item->approved_quantity }}
                        </label>

                        <input type="number"
                            step="0.01"
                            min="0"
                            max="{{ $item->approved_quantity }}"
                            name="received_quantities[{{ $item->id }}]"
                            value="{{ $item->approved_quantity }}"
                            class="form-control"
                            required>
                    </div>
                @endif
            @endforeach

            <div class="mb-3">
                <label>ملاحظات الاستلام</label>
                <textarea name="notes" class="form-control"></textarea>
            </div>

            <button type="submit" class="btn btn-success">
                تأكيد الاستلام
            </button>

            <a href="{{ route('fuel-orders.show', $order->id) }}" class="btn btn-secondary">
                رجوع
            </a>
        </form>
    </div>
</div>
@endsection