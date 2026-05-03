@extends('layouts.app')

@section('title', 'تفاصيل البلاغ')
@section('page_title', 'تفاصيل البلاغ')
@section('page_subtitle', 'عرض ومتابعة بلاغ الصيانة')

@section('content')

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert-danger">
        <ul style="margin:0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="page-actions">
    <div class="muted">مراجعة بيانات البلاغ ومتابعة مساره بين الأقسام.</div>
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
            <label>المسؤول عن التنفيذ</label>
            <div>{{ $maintenanceRequest->assignedUser->name ?? '-' }}</div>
        </div>

        <div class="form-group">
            <label>الأولوية</label>
            <div>{{ $priorities[$maintenanceRequest->priority] ?? $maintenanceRequest->priority }}</div>
        </div>

        <div class="form-group">
            <label>الحالة الحالية</label>
            <div>{{ $statuses[$maintenanceRequest->status] ?? $maintenanceRequest->status }}</div>
        </div>

        <div class="form-group">
            <label>المرحلة الحالية</label>
            <div>
                @switch($maintenanceRequest->current_step)
                    @case('operations_review')
                        مراجعة التشغيل
                        @break
                    @case('department_review')
                        مراجعة القسم
                        @break
                    @case('technician_work')
                        تنفيذ المسؤول
                        @break
                    @case('operations_final_review')
                        اعتماد التشغيل النهائي
                        @break
                    @case('closed')
                        مغلق
                        @break
                    @case('cancelled')
                        ملغي
                        @break
                    @default
                        {{ $maintenanceRequest->current_step ?? '-' }}
                @endswitch
            </div>
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

{{-- 1) مدير التشغيل يحول البلاغ إلى القسم المختص --}}
@if($maintenanceRequest->current_step === 'operations_review')
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">مراجعة التشغيل</h3>

        <form method="POST" action="{{ route('maintenance-requests.assign-department', $maintenanceRequest->id) }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>القسم المختص</label>
                    <select name="department_id" required>
                        <option value="">-- اختر القسم --</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $maintenanceRequest->department_id) == $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label>ملاحظات التشغيل</label>
                    <textarea name="notes" rows="3" placeholder="اكتب ملاحظة اختيارية">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">تحويل للقسم</button>
            </div>
        </form>
    </div>
@endif

{{-- 2) القسم يعين الشخص المسؤول --}}
@if($maintenanceRequest->current_step === 'department_review')
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">تعيين المسؤول عن الحل</h3>

        <form method="POST" action="{{ route('maintenance-requests.assign-technician', $maintenanceRequest->id) }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>المسؤول</label>
                    <select name="assigned_to" required>
                        <option value="">-- اختر المسؤول --</option>
                        @foreach($technicians as $technician)
                            <option value="{{ $technician->id }}" @selected(old('assigned_to', $maintenanceRequest->assigned_to) == $technician->id)>
                                {{ $technician->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label>ملاحظات القسم</label>
                    <textarea name="notes" rows="3" placeholder="اكتب ملاحظة اختيارية">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">تعيين المسؤول</button>
            </div>
        </form>
    </div>
@endif

{{-- 3) المسؤول يؤكد تنفيذ الحل --}}
@if($maintenanceRequest->current_step === 'technician_work')
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">تنفيذ الإصلاح</h3>

        <form method="POST" action="{{ route('maintenance-requests.mark-resolved', $maintenanceRequest->id) }}">
            @csrf

            <div class="form-grid">
                <div class="form-group full">
                    <label>تفاصيل الإصلاح</label>
                    <textarea name="notes" rows="4" placeholder="اكتب ماذا تم إصلاحه" required>{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">تم الإصلاح وإرساله للتشغيل</button>
            </div>
        </form>
    </div>
@endif

{{-- 4) التشغيل يعتمد الإصلاح أو يرجعه --}}
@if($maintenanceRequest->current_step === 'operations_final_review')
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">اعتماد التشغيل النهائي</h3>

        <div class="form-actions" style="justify-content:flex-start; gap:10px; align-items:flex-start;">
            <form method="POST" action="{{ route('maintenance-requests.approve-closure', $maintenanceRequest->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">اعتماد وإغلاق البلاغ</button>
            </form>

            <form method="POST" action="{{ route('maintenance-requests.return-to-department', $maintenanceRequest->id) }}" class="d-inline">
                @csrf

                <input type="text"
                       name="reason"
                       placeholder="سبب الإرجاع"
                       required
                       style="margin-bottom:8px;">

                <button type="submit" class="btn btn-danger">إرجاع للقسم</button>
            </form>
        </div>
    </div>
@endif

{{-- 5) حالة نهائية --}}
@if(in_array($maintenanceRequest->current_step, ['closed', 'cancelled']))
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-top:0;">حالة البلاغ</h3>

        @if($maintenanceRequest->current_step === 'closed')
            <div class="alert-success">تم إغلاق البلاغ واعتماد الإصلاح من التشغيل.</div>
        @else
            <div class="alert-danger">تم إلغاء البلاغ.</div>
        @endif
    </div>
@endif

@endsection