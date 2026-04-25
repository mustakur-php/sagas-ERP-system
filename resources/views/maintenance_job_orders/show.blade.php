@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">تفاصيل طلب الصيانة</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><strong>رقم طلب الصيانة:</strong> {{ $job->id }}</div>
                <div class="col-md-3"><strong>رقم البلاغ:</strong> {{ $job->maintenanceRequest->report_number ?? '-' }}</div>
                <div class="col-md-3"><strong>المحطة:</strong> {{ $job->maintenanceRequest->station->name ?? '-' }}</div>
                <div class="col-md-3"><strong>الفني:</strong> {{ $job->technician->name ?? '-' }}</div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4"><strong>الحالة:</strong> {{ $job->status }}</div>
                <div class="col-md-4"><strong>تاريخ التعيين:</strong> {{ optional($job->assigned_at)->format('Y-m-d H:i') ?? '-' }}</div>
                <div class="col-md-4"><strong>تاريخ البدء:</strong> {{ optional($job->started_at)->format('Y-m-d H:i') ?? '-' }}</div>
            </div>

            <div class="row mt-3">
                <div class="col-md-4"><strong>تاريخ الإكمال:</strong> {{ optional($job->completed_at)->format('Y-m-d H:i') ?? '-' }}</div>
                <div class="col-md-8"><strong>عنوان البلاغ:</strong> {{ $job->maintenanceRequest->title ?? '-' }}</div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12"><strong>وصف البلاغ:</strong> {{ $job->maintenanceRequest->description ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">تعيين الفني</div>
        <div class="card-body">
            <form method="POST" action="{{ route('maintenance-job-orders.assign-technician', $job->id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <select name="assigned_to" class="form-control" required>
                            <option value="">اختر الفني</option>
                            @foreach($technicians as $technician)
                                <option value="{{ $technician->id }}" {{ $job->assigned_to == $technician->id ? 'selected' : '' }}>
                                    {{ $technician->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button type="submit" class="btn btn-warning">تعيين الفني</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">تحديث الحالة</div>
        <div class="card-body">
            <form method="POST" action="{{ route('maintenance-job-orders.update-status', $job->id) }}" style="display:flex; gap:10px;">
                @csrf

                <select name="status" class="form-control">
                    <option value="pending" @selected($job->status == 'pending')>قيد الانتظار</option>
                    <option value="assigned" @selected($job->status == 'assigned')>تم التعيين</option>
                    <option value="in_progress" @selected($job->status == 'in_progress')>قيد التنفيذ</option>
                    <option value="completed" @selected($job->status == 'completed')>مكتمل</option>
                </select>

                <button type="submit" class="btn btn-success">تحديث</button>
            </form>
        </div>
    </div>
</div>
@endsection