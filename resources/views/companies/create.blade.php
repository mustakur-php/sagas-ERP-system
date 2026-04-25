@extends('layouts.app')

@section('title', 'إضافة شركة')

@section('content')

<h2>إضافة شركة</h2>

<form method="POST" action="{{ route('companies.store') }}">
    @csrf

    <div>
        <label>اسم الشركة</label>
        <input type="text" name="name" required>
    </div>

    <div>
        <label>الكود</label>
        <input type="text" name="code">
    </div>

    <div>
        <label>الحالة</label>
        <select name="status">
            <option value="active">نشط</option>
            <option value="inactive">غير نشط</option>
        </select>
    </div>

    <div>
        <label>ملاحظات</label>
        <textarea name="notes"></textarea>
    </div>

    <button class="btn btn-primary">حفظ</button>
</form>

@endsection