@extends('layouts.app')

@section('title', 'الناقلين')
@section('page_title', 'الناقلين')
@section('page_subtitle', 'إدارة الناقلين المستخدمين في طلبات الوقود')

@section('content')
<div class="card clean-card">
    <div class="card-header clean-card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>قائمة الناقلين</strong>
            <div class="text-muted small">إضافة وتعديل الناقلين</div>
        </div>
        <a href="{{ route('carriers.create') }}" class="btn btn-primary btn-sm">إضافة ناقل</a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>اسم الناقل</th>
                        <th>الجوال</th>
                        <th>المسؤول</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($carriers as $carrier)
                        <tr>
                            <td>{{ $carrier->name }}</td>
                            <td>{{ $carrier->phone ?? '-' }}</td>
                            <td>{{ $carrier->contact_person ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $carrier->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $carrier->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>{{ $carrier->notes ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('carriers.edit', $carrier->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                <form action="{{ route('carriers.destroy', $carrier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف الناقل؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
