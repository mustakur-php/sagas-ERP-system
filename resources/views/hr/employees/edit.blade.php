@extends('layouts.app')

@section('content')

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">تعديل موظف</h2>
            <p class="text-muted">تحديث بيانات الموظف</p>
        </div>
    </div>

    <div class="card form-card">

        <form method="POST" action="{{ route('hr.employees.update', $employee->id) }}">
            @csrf
            @method('PUT')

            @include('hr.employees._form')

        </form>

    </div>

</div>

@endsection