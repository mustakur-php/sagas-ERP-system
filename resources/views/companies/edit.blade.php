@extends('layouts.app')

@section('title', 'تعديل شركة')

@section('content')

<h2>تعديل شركة</h2>

<form method="POST" action="{{ route('companies.update', $company->id) }}">
    @csrf
    @method('PUT')

    <div>
        <label>اسم الشركة</label>
        <input type="text" name="name" value="{{ $company->name }}">
    </div>

    <div>
        <label>الكود</label>
        <input type="text" name="code" value="{{ $company->code }}">
    </div>

    <div>
        <label>الحالة</label>
        <select name="status">
            <option value="active" @selected($company->status == 'active')>نشط</option>
            <option value="inactive" @selected($company->status == 'inactive')>غير نشط</option>
        </select>
    </div>

    <div>
        <label>ملاحظات</label>
        <textarea name="notes">{{ $company->notes }}</textarea>
    </div>

    <button class="btn btn-primary">تحديث</button>
</form>

@endsection