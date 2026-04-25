@extends('layouts.app')

@section('title', 'تفاصيل البلاغ')
@section('page_title', 'تفاصيل البلاغ')
@section('page_subtitle', 'عرض ومتابعة بلاغ الصيانة')

@section('content')

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="page-actions">
    <div class="muted">مراجعة بيانات البلاغ وتحديث حالته.</div>
    <a href="{{ route('maintenance-requests.index') }}" class="btn btn-secondary">رجوع للبلاغات</a>
</div>

<div class="card" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">بيانات البلاغ</h3>

    <div class="form-grid">
        <div class="form-group">
            <label>رقم البلاغ</label>
            <div>{{ $maintenanceRequest->report_number }}</div>
        </div>

        <div class="form-group">
            <label>المحطة</label>
            <div>{{ $maintenanceRequest->station->name ?? '-' }}</div>
        </div>

        <div class="form-group">
            <label>الشركة</label>
            <div>{{ $maintenanceRequest->station->company->name ?? '-' }}</div>
        </div>

        <div class="form-group">
            <label>القسم المختص</label>
            <div>{{ $maintenanceRequest->department->name ?? '-' }}</div>
        </div>

        <div class="form-group">
            <label>الأولوية</label>
            <div>{{ $priorities[$maintenanceRequest->priority] ?? $maintenanceRequest->priority }}</div>
        </div>

        <div class="form-group">
            <label>الحالة الحالية</label>
            <div>{{ $statuses[$maintenanceRequest->status] ?? $maintenanceRequest->status }}</div>
        </div>

        <div class="form-group full">
            <label>عنوان البلاغ</label>
            <div>{{ $maintenanceRequest->title }}</div>
        </div>

        <div class="form-group full">
            <label>الوصف</label>
            <div>{{ $maintenanceRequest->description ?: '-' }}</div>
        </div>

        <div class="form-group full">
            <label>الملاحظات</label>
            <div>{{ $maintenanceRequest->notes ?: '-' }}</div>
        </div>

        <div class="form-group">
            <label>وقت التسجيل</label>
            <div>{{ $maintenanceRequest->reported_at ?: '-' }}</div>
        </div>

        <div class="form-group">
            <label>وقت الإغلاق</label>
            <div>{{ $maintenanceRequest->closed_at ?: '-' }}</div>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-top:0;">تحديث حالة البلاغ</h3>

    <form method="POST" action="{{ route('maintenance-requests.update-Status', $maintenanceRequest->id) }}">
        @csrf
        @method('PATCH')

        <div class="form-grid">
            <div class="form-group">
                <label>الحالة الجديدة</label>
                <select name="status" required>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($maintenanceRequest->status == $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">تحديث الحالة</button>
        </div>
    </form>
</div>

@if(!$maintenanceRequest->jobOrder)
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">تحويل إلى قسم الصيانة</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('maintenance-job-orders.create-from-request', $maintenanceRequest->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="assigned_to" class="form-label">اختر الفني</label>
                    <select name="assigned_to" id="assigned_to" class="form-select" required>
                        <option value="">-- اختر الفني --</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}">
                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    إنشاء أمر عمل وتحويل للصيانة
                </button>
            </form>
        </div>
    </div>
@else
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">تم إنشاء أمر العمل</h5>
        </div>
        <div class="card-body">
            <p class="mb-2">
                <strong>رقم أمر العمل:</strong>
                {{ $maintenanceRequest->jobOrder->id }}
            </p>
            <p class="mb-2">
                <strong>حالة أمر العمل:</strong>
                {{ $maintenanceRequest->jobOrder->status }}
            </p>
            <a href="{{ route('maintenance-job-orders.show', $maintenanceRequest->jobOrder->id) }}" class="btn btn-outline-primary">
                عرض أمر العمل
            </a>
        </div>
    </div>
@endif

@endsection