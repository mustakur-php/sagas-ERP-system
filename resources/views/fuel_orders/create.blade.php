@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">طلب وقود جديد</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('fuel-orders.store') }}">
        @csrf

        <div class="row">
            @foreach($fuelTypes as $fuel)
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">{{ $fuel->name }}</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="quantities[{{ $fuel->id }}]"
                        value="{{ old('quantities.' . $fuel->id) }}"
                        class="form-control"
                        placeholder="الكمية باللتر">
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="4" placeholder="أي ملاحظات إضافية">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">إرسال الطلب</button>
        <a href="{{ route('fuel-orders.index') }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection