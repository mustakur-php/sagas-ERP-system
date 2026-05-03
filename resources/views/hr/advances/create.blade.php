@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">إضافة سلفة</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('hr.advances.store') }}">
        @csrf

        <div class="mb-3">
            <label>الموظف</label>
            <select name="employee_id" class="form-control" required>
                <option value="">اختر الموظف</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">
                        {{ $employee->name_ar }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>المبلغ</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>التاريخ</label>
            <input type="date" name="advance_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>الحالة</label>
            <select name="status" class="form-control">
                <option value="pending">قيد الانتظار</option>
                <option value="approved">معتمد</option>
                <option value="rejected">مرفوض</option>
            </select>
        </div>

        <div class="mb-3">
            <label>سبب</label>
            <textarea name="reason" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">حفظ</button>
    </form>

</div>
@endsection