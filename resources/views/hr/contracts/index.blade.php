@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>العقود</h3>

        <a href="{{ route('hr.contracts.create') }}" class="btn btn-primary">
            إضافة عقد جديد
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>رقم العقد</th>
                        <th>نوع العقد</th>
                        <th>الراتب الأساسي</th>
                        <th>بدل السكن</th>
                        <th>بدل النقل</th>
                        <th>بدلات أخرى</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>تاريخ البداية</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->employee->name_ar ?? '-' }}</td>
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->contract_type }}</td>
                            <td>{{ number_format($contract->basic_salary, 2) }}</td>
                            <td>{{ number_format($contract->housing_allowance, 2) }}</td>
                            <td>{{ number_format($contract->transport_allowance, 2) }}</td>
                            <td>{{ number_format($contract->other_allowances, 2) }}</td>
                            <td>
                                <strong>
                                    {{ number_format($contract->basic_salary + $contract->housing_allowance + $contract->transport_allowance + $contract->other_allowances, 2) }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $contract->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $contract->status }}
                                </span>
                            </td>
                            <td>{{ $contract->start_date }}</td>

                            <td>
                                <a href="{{ route('hr.contracts.edit', $contract->id) }}" class="btn btn-sm btn-warning">
                                    تعديل
                                </a>

                                <form action="{{ route('hr.contracts.destroy', $contract->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-muted">
                                لا توجد عقود حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $contracts->links() }}
            </div>

        </div>
    </div>

</div>
@endsection