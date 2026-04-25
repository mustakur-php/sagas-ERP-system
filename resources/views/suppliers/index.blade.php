@extends('layouts.app')

@section('title', 'الموردين')
@section('page_title', 'الموردين')
@section('page_subtitle', 'إدارة الموردين وأسعار الوقود')

@section('content')
<div class="card clean-card">
    <div class="card-header clean-card-header d-flex justify-content-between align-items-center">
        <div>
            <strong>قائمة الموردين</strong>
            <div class="text-muted small">إضافة وتعديل الموردين وأسعار الوقود</div>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm">إضافة مورد</a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>اسم المورد</th>
                        <th>الجوال</th>
                        <th>المسؤول</th>
                        <th>الحالة</th>
                        <th>أسعار الوقود</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone ?? '-' }}</td>
                            <td>{{ $supplier->contact_person ?? '-' }}</td>
                            <td>
                                <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td>
                                @forelse($supplier->fuelPrices as $price)
                                    <span class="badge bg-light text-dark border me-1">
                                        {{ $price->fuelType->name ?? 'وقود' }}: {{ number_format($price->price_per_liter, 2) }}
                                    </span>
                                    
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف المورد؟')">
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
