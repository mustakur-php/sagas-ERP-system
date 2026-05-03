@extends('layouts.app')

@section('content')

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">إضافة موظف</h2>
            <p class="text-muted">إدخال بيانات موظف جديد</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert-success" style="background:#fee2e2;color:#991b1b;border-color:#fecaca;">
            <ul style="margin:0;padding-right:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-card">

        <form method="POST" action="{{ route('hr.employees.store') }}">
            @csrf

            @include('hr.employees._form')

        </form>

    </div>

</div>

@endsection