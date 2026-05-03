@extends('layouts.app')

@section('content')

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">الموظفين</h2>
            <p class="text-muted">إدارة جميع موظفي الشركة</p>
        </div>

        <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
            + إضافة موظف
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">

        @if($employees->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>رقم الموظف</th>
                            <th>القسم</th>
                            <th>المسمى الوظيفي</th>
                            <th>موقع العمل</th>
                            <th>الحالة</th>
                            <th>تاريخ التوظيف</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->name_ar ?? $employee->name_en ?? '-' }}</td>

                                <td>{{ $employee->employee_number }}</td>

                                <td>{{ $employee->department->name_ar ?? $employee->department->name_en ?? '-' }}</td>

                                <td>{{ $employee->position->title_ar ?? $employee->position->title_en ?? '-' }}</td>
                                <td>{{ $employee->workLocation->name ?? '-' }}</td>
                                <td>{{ $employee->employment_status }}</td>
                                <td>
                                    @if($employee->employment_status === 'active')
                                        <span class="status-badge status-approved">نشط</span>
                                    @elseif($employee->status === 'inactive')
                                        <span class="status-badge status-rejected">غير نشط</span>
                                    @elseif($employee->status === 'on_leave')
                                        <span class="status-badge status-pending">إجازة</span>
                                    @else
                                        <span class="status-badge status-partial">منتهي</span>
                                    @endif
                                </td>

                                <td>
                                    {{ $employee->hire_date?->format('Y-m-d') ?? '-' }}
                                </td>

                                <td>
                                    <div class="actions">

                                        <a href="{{ route('hr.employees.show', $employee->id) }}"
                                           class="btn btn-info btn-sm">
                                            عرض
                                        </a>

                                        <a href="{{ route('hr.employees.edit', $employee->id) }}"
                                           class="btn btn-warning btn-sm">
                                            تعديل
                                        </a>

                                        <form action="{{ route('hr.employees.destroy', $employee->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من الحذف؟')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                حذف
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:20px;">
                {{ $employees->links() }}
            </div>

        @else
            <div class="text-center muted">
                لا يوجد موظفين حتى الآن
            </div>
        @endif

    </div>

</div>

@endsection