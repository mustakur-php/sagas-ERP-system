@extends('layouts.app')

@section('content')
<div class="container">
    <h2>مسير الرواتب</h2>

    <a href="{{ route('hr.payroll.create') }}" class="btn btn-primary mb-3">
        إنشاء مسير جديد
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>السنة</th>
                <th>الشهر</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th>عرض</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrollRuns as $run)
                <tr>
                    <td>{{ $run->year }}</td>
                    <td>{{ $run->month }}</td>
                    <td>{{ $run->net_amount }}</td>
                    <td>{{ $run->status }}</td>
                    <td>
                        <a href="{{ route('hr.payroll.show', $run->id) }}" class="btn btn-sm btn-info">
                            عرض
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $payrollRuns->links() }}
</div>
@endsection