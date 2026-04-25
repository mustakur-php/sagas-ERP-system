@extends('layouts.app')

@section('title', 'الشركات')
@section('page_title', 'الشركات')

@section('content')

<div class="page-actions">
    <a href="{{ route('companies.create') }}" class="btn btn-primary">
        + إضافة شركة
    </a>
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>الكود</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->code ?? '-' }}</td>
                    <td>{{ $company->status }}</td>
                    <td>
                        <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-warning">تعديل</a>

                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection