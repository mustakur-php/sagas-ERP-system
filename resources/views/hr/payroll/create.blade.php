@extends('layouts.app')

@section('content')
<div class="container">
    <h2>إنشاء مسير رواتب</h2>

    <form method="POST" action="{{ route('hr.payroll.store') }}">
        @csrf

        <div class="mb-3">
            <label>السنة</label>
            <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
        </div>

        <div class="mb-3">
            <label>الشهر</label>
            <input type="number" name="month" class="form-control" min="1" max="12" value="{{ date('n') }}" required>
        </div>

        <button type="submit" class="btn btn-success">
            توليد الرواتب
        </button>
    </form>
</div>
@endsection