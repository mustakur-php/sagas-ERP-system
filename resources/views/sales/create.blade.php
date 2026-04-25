@extends('layouts.app')

@section('content')

<h2>إضافة مبيعات</h2>

<form method="POST" action="{{ route('sales.store') }}">
    @csrf

    <div>
        <label>المحطة</label>
        <select name="station_id">
            @foreach($stations as $station)
                <option value="{{ $station->id }}">{{ $station->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>المبلغ</label>
        <input type="number" name="amount">
    </div>

    <div>
        <label>التاريخ</label>
        <input type="date" name="sale_date">
    </div>

    <button class="btn btn-primary">حفظ</button>
</form>

@endsection