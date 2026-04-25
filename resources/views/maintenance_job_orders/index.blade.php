@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">طلبات الصيانة</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رقم البلاغ</th>
                        <th>المحطة</th>
                        <th>الفني</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td>{{ $job->id }}</td>
                            <td>{{ $job->maintenanceRequest->report_number ?? '-' }}</td>
                            <td>{{ $job->maintenanceRequest->station->name ?? '-' }}</td>
                            <td>{{ $job->technician->name ?? '-' }}</td>
                            <td>
                                @if($job->status === 'completed')
                                    <span class="status-badge status-approved">مكتمل</span>
                                @elseif($job->status === 'in_progress')
                                    <span class="status-badge status-partial">قيد التنفيذ</span>
                                @elseif($job->status === 'assigned')
                                    <span class="status-badge status-pending">تم التعيين</span>
                                @elseif($job->status === 'cancelled')
                                    <span class="status-badge status-rejected">ملغي</span>
                                @else
                                    <span class="status-badge status-pending">معلق</span>
                                @endif
                            </td>
                            <td>{{ $job->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('maintenance-job-orders.show', $job->id) }}" class="btn btn-primary">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد طلبات صيانة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection