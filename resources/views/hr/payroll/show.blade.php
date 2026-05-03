@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>تفاصيل مسير الرواتب</h3>
            <div class="text-muted">
                السنة: {{ $payroll->year }} — الشهر: {{ $payroll->month }}
            </div>
        </div>

        <a href="{{ route('hr.payroll.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <small>إجمالي الأساسي</small>
                <strong>{{ number_format($payroll->total_basic_salary, 2) }}</strong>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <small>إجمالي البدلات</small>
                <strong>{{ number_format($payroll->total_allowances, 2) }}</strong>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <small>إجمالي الخصومات</small>
                <strong>{{ number_format($payroll->total_deductions, 2) }}</strong>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <small>الصافي</small>
                <strong>{{ number_format($payroll->net_amount, 2) }}</strong>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            رواتب الموظفين
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>الأساسي</th>
                        <th>بدل السكن</th>
                        <th>بدل النقل</th>
                        <th>بدلات أخرى</th>
                        <th>إجمالي البدلات</th>
                        <th>الخصومات</th>
                        <th>الصافي</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payroll->items as $item)
                        <tr>
                            <td>{{ $item->employee->name ?? '-' }}</td>
                            <td>{{ number_format($item->basic_salary, 2) }}</td>
                            <td>{{ number_format($item->housing_allowance, 2) }}</td>
                            <td>{{ number_format($item->transport_allowance, 2) }}</td>
                            <td>{{ number_format($item->other_allowances, 2) }}</td>
                            <td>{{ number_format($item->allowances, 2) }}</td>
                            <td>{{ number_format($item->deductions, 2) }}</td>
                            <td>
                                <strong>{{ number_format($item->net_salary, 2) }}</strong>

                                @if($item->details)
                                    <div class="mt-2 small text-muted">

                                        <div>خصم إجازات: {{ number_format($item->details['leave_deduction'] ?? 0, 2) }}</div>

                                        <div>سلف: {{ number_format($item->details['advance_deduction'] ?? 0, 2) }}</div>

                                        <div>تأخير: {{ number_format($item->details['late_deduction'] ?? 0, 2) }}</div>

                                        <div>خروج مبكر: {{ number_format($item->details['early_deduction'] ?? 0, 2) }}</div>

                                        <div class="text-success">
                                            إضافي: {{ number_format($item->details['overtime_amount'] ?? 0, 2) }}
                                        </div>

                                        <hr class="my-1">

                                        <div>دقائق تأخير: {{ $item->details['late_minutes'] ?? 0 }}</div>
                                        <div>خروج مبكر: {{ $item->details['early_leave_minutes'] ?? 0 }}</div>
                                        <div>إضافي: {{ $item->details['overtime_minutes'] ?? 0 }}</div>

                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                لا توجد رواتب داخل هذا المسير
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection