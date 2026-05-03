@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>السلف</h3>

        <a href="{{ route('hr.advances.create') }}" class="btn btn-primary">
            إضافة سلفة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">

            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>المبلغ</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($advances as $advance)
                        <tr>
                            <td>{{ $advance->employee->name_ar ?? '-' }}</td>
                            <td>{{ number_format($advance->amount, 2) }}</td>
                            <td>{{ $advance->advance_date }}</td>
                            <td>
                                <span class="badge bg-{{ $advance->status == 'approved' ? 'success' : 'secondary' }}">
                                    {{ $advance->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('hr.advances.edit', $advance->id) }}" class="btn btn-sm btn-warning">
                                    تعديل
                                </a>

                                <form action="{{ route('hr.advances.destroy', $advance->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="5" class="text-muted">لا توجد سلف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $advances->links() }}

        </div>
    </div>

</div>
@endsection