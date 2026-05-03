@extends('layouts.app')

@section('title', 'البلاغات')
@section('page_title', 'بلاغات الصيانة')
@section('page_subtitle', 'إدارة ومتابعة بلاغات المحطات')

@section('content')
@php
    $authUser = auth()->user();
@endphp
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert-success" style="background:#fee2e2; color:#991b1b; border-color:#fecaca;">
        {{ session('error') }}
    </div>
@endif

<div class="page-actions">
    <div class="muted">يمكنك من هنا متابعة البلاغات وتصفية النتائج.</div>
    @if($authUser && $authUser->hasPermission('create_maintenance_requests'))
        <a href="{{ route('maintenance-requests.create') }}" class="btn btn-primary">
            + إضافة بلاغ
        </a>
    @endif
</div>

<div class="card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('maintenance-requests.index') }}">
        <div class="form-grid">
            <div class="form-group">
                <label>القسم</label>
                <select name="department_id">
                    <option value="">كل الأقسام</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>الحالة</label>
                <select name="status">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') == $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="align-self:end;">
                <button type="submit" class="btn btn-primary">فلترة</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>رقم البلاغ</th>
                    <th>المحطة</th>
                    <th>القسم</th>
                    <th>العنوان</th>
                    <th>الأولوية</th>
                    <th>الحالة</th>
                    <th>المرحلة</th>
                    <th>تاريخ البلاغ</th>
                    <th>الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenanceRequests as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->report_number }}</td>
                        <td>{{ $item->station->name ?? '-' }}</td>
                        <td>{{ $item->department->name ?? '-' }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $priorities[$item->priority] ?? $item->priority ?? '-' }}</td>
                        <td>{{ $statuses[$item->status] ?? $item->status }}</td>
                        <td>
                            @switch($item->current_step)
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
                                    {{ $item->current_step ?? '-' }}
                            @endswitch
                        </td>
                        <td>{{ $item->reported_at ? \Carbon\Carbon::parse($item->reported_at)->format('Y-m-d H:i') : '-' }}</td>
                        <td class="actions">

                            <a href="{{ route('maintenance-requests.show', $item->id) }}" class="btn btn-primary">
                                عرض
                            </a>

                            @if($authUser && $authUser->hasPermission('edit_maintenance_requests'))
                                <a href="{{ route('maintenance-requests.edit', $item->id) }}"
                                class="btn btn-warning">
                                    تعديل
                                </a>
                            @endif

                            @if($authUser && $authUser->hasPermission('delete_maintenance_requests'))
                                <form action="{{ route('maintenance-requests.destroy', $item->id) }}"
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger">حذف</button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">لا توجد بلاغات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $maintenanceRequests->links() }}
    </div>
</div>

@endsection