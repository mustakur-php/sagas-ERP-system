@extends('layouts.app')

@section('title', 'إضافة بلاغ')
@section('page_title', 'إضافة بلاغ صيانة')
@section('page_subtitle', 'تسجيل بلاغ جديد للمحطة')

@section('content')

@if($errors->any())
    <div class="alert-success" style="background:#fee2e2; color:#991b1b; border-color:#fecaca;">
        <ul style="margin:0; padding-right:18px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card form-card">
    <form method="POST" action="{{ route('maintenance-requests.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>القسم المختص</label>
                <select name="department_id">
                    <option value="">اختر القسم</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>عنوان البلاغ</label>
                <input type="text" name="title" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label>الأولوية</label>
                <select name="priority" required>
                    @foreach($priorities as $key => $label)
                        <option value="{{ $key }}" @selected(old('priority', 'medium') == $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full">
                <label>وصف البلاغ</label>
                <textarea name="description" rows="5">{{ old('description') }}</textarea>
            </div>

            <div class="form-group full">
                <label>ملاحظات</label>
                <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">حفظ البلاغ</button>
            <a href="{{ route('maintenance-requests.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>

@endsection