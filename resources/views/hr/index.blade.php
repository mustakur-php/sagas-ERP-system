@extends('layouts.app')

@section('content')
@php
    $iconClass = function ($icon) {
        return match ($icon) {
            'users' => 'bi-people',
            'file' => 'bi-file-earmark-text',
            'bell' => 'bi-bell',
            'clock' => 'bi-clock',
            'building' => 'bi-building',
            'wallet' => 'bi-wallet2',
            default => 'bi-circle',
        };
    };
@endphp
<style>
    .hr-page { background:#f8fafc; min-height:calc(100vh - 80px); }
    .hr-card {
        background:#fff;
        border:1px solid #eef2f7;
        border-radius:22px;
        box-shadow:0 1px 2px rgba(15,23,42,.04);
        transition:.2s ease;
    }
    .hr-card:hover {
        transform:translateY(-2px);
        box-shadow:0 10px 24px rgba(15,23,42,.08);
    }
    .icon-box {
        width:44px;
        height:44px;
        border-radius:14px;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .bg-blue-soft { background:#dbeafe; color:#2563eb; }
    .bg-emerald-soft { background:#d1fae5; color:#059669; }
    .bg-amber-soft { background:#fef3c7; color:#d97706; }
    .bg-rose-soft { background:#ffe4e6; color:#e11d48; }
    .trend-plus { background:#ecfdf5; color:#059669; }
    .trend-minus { background:#fff1f2; color:#e11d48; }
    .trend-zero { background:#f8fafc; color:#64748b; }
    .hr-empty-box {
        border:1px dashed #cbd5e1;
        border-radius:18px;
        padding:24px;
        background:#f8fafc;
        color:#64748b;
    }
</style>

<div class="hr-page p-4 p-lg-5">

    <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">الموارد البشرية</h2>
            <p class="text-muted mb-0">لوحة تحكم وإدارة قسم الموارد البشرية</p>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-white border rounded-4 fw-semibold shadow-sm">تصدير تقرير</button>
            <a href="{{ route('hr.employees.create') }}"
            class="btn text-white rounded-4 fw-semibold shadow-sm"
            style="background:#1e40af">
                + موظف جديد
            </a>
        </div>
    </div>

    {{-- كروت روابط القسم بدل السايدبار الداخلي --}}
    

    @if($tab === 'overview')
        <div class="row g-4 mb-4">
            @foreach($stats as $stat)
                @php
                    $trendClass = str_starts_with($stat['trend'], '+') ? 'trend-plus' : (str_starts_with($stat['trend'], '-') ? 'trend-minus' : 'trend-zero');
                @endphp

                <div class="col-12 col-md-6 col-xl-3">
                    <div class="hr-card p-4 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="icon-box bg-{{ $stat['color'] }}-soft">
                                <i class="bi {{ $iconClass($stat['icon']) }}"></i>
                            </div>
                            @if(!empty($stat['trend']))
                                <span class="badge rounded-3 {{ $trendClass }}">{{ $stat['trend'] }}</span>
                            @endif
                        </div>
                        <div class="text-muted small fw-semibold">{{ $stat['label'] }}</div>
                        <div class="fs-3 fw-bold text-dark">{{ $stat['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="hr-card p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="fw-bold mb-1">الحضور الأسبوعي</h5>
                            <p class="text-muted small mb-0">إحصائية الحضور خلال آخر الأيام</p>
                        </div>
                    </div>
                    <div style="height: 320px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="hr-card p-4 h-100">
                    <h5 class="fw-bold mb-4">آخر الأنشطة</h5>

                    <div class="d-grid gap-4">
                        @foreach($recentActivities as $activity)
                            <div class="d-flex gap-3">
                                <div class="icon-box bg-blue-soft">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small text-dark">{{ $activity['user'] ?? '-' }}</div>
                                    <div class="text-muted small">{{ $activity['action'] ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:11px;">{{ $activity['time'] ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    @elseif($tab === 'employees')
        <div class="hr-card p-4">
            <h4 class="fw-bold mb-4">الموظفين</h4>

            @if($employees->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>رقم الموظف</th>
                                <th>الحالة</th>
                                <th>تاريخ التعيين</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name ?? '-' }}</td>
                                    <td>{{ $employee->employee_number ?? '-' }}</td>
                                    <td>{{ $employee->status ?? '-' }}</td>
                                    <td>{{ $employee->hire_date ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="hr-empty-box">لا يوجد موظفين حتى الآن.</div>
            @endif
        </div>

    @elseif($tab === 'organization')
        <div class="row g-4">
            <div class="col-12 col-xl-6">
                <div class="hr-card p-4 h-100">
                    <h4 class="fw-bold mb-4">الإدارات</h4>

                    @forelse($departments as $department)
                        <div class="d-flex justify-content-between border-bottom py-3">
                            <span class="fw-semibold">{{ $department->name ?? '-' }}</span>
                            <span class="badge text-bg-light">{{ $department->employees_count ?? 0 }} موظف</span>
                        </div>
                    @empty
                        <div class="hr-empty-box">لا توجد إدارات حتى الآن.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="hr-card p-4 h-100">
                    <h4 class="fw-bold mb-4">المناصب</h4>

                    @forelse($positions as $position)
                        <div class="border-bottom py-3">
                            <div class="fw-semibold">{{ $position->name ?? '-' }}</div>
                            <div class="text-muted small">{{ $position->description ?? '' }}</div>
                        </div>
                    @empty
                        <div class="hr-empty-box">لا توجد مناصب حتى الآن.</div>
                    @endforelse
                </div>
            </div>
        </div>

    @elseif($tab === 'payroll')
        <div class="hr-card p-4">
            <h4 class="fw-bold mb-4">الرواتب</h4>

            @forelse($payrollRuns as $run)
                <div class="d-flex justify-content-between border-bottom py-3">
                    <div>
                        <div class="fw-semibold">{{ $run->title ?? 'مسير رواتب' }}</div>
                        <div class="text-muted small">{{ $run->period_start ?? '-' }} إلى {{ $run->period_end ?? '-' }}</div>
                    </div>
                    <span class="badge text-bg-light">{{ $run->status ?? '-' }}</span>
                </div>
            @empty
                <div class="hr-empty-box">لا توجد مسيرات رواتب حتى الآن.</div>
            @endforelse
        </div>

    @elseif($tab === 'attendance')
        <div class="hr-card p-4">
            <h4 class="fw-bold mb-4">الحضور والانصراف</h4>

            <div style="height:320px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

    @elseif($tab === 'settings')
        <div class="hr-card p-4">
            <h4 class="fw-bold mb-4">الإعدادات</h4>

            @foreach($settings as $setting)
                <div class="d-flex align-items-center gap-3 border-bottom py-3">
                    <div class="icon-box bg-blue-soft">
                        <i class="bi {{ $iconClass($setting['icon'] ?? 'circle') }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $setting['label'] ?? '-' }}</div>
                        <div class="text-muted small">{{ $setting['desc'] ?? '' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const attendance = @json($attendance);
    const canvas = document.getElementById('attendanceChart');

    if (canvas) {
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: attendance.map(item => item.name),
                datasets: [
                    {
                        label: 'حاضر',
                        data: attendance.map(item => item.present),
                        backgroundColor: '#1e40af',
                        borderRadius: 8,
                    },
                    {
                        label: 'غائب',
                        data: attendance.map(item => item.absent),
                        backgroundColor: '#e2e8f0',
                        borderRadius: 8,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#e2e8f0' } }
                }
            }
        });
    }
</script>
@endpush