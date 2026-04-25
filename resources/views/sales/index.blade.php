@extends('layouts.app')

@section('title', 'المبيعات')

@section('content')

<a href="{{ route('sales.create') }}" class="btn btn-primary">+ إضافة</a>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>المحطة</th>
            <th>المبلغ</th>
            <th>التاريخ</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->station->name ?? '-' }}</td>
            <td>{{ $sale->amount }}</td>
            <td>{{ $sale->sale_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection