@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">تعديل البلاغ</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('maintenance-requests.update', $maintenanceRequest->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $maintenanceRequest->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">الوصف</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $maintenanceRequest->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">الأولوية</label>
            <select name="priority" class="form-control" required>
                @foreach($priorities as $key => $label)
                    <option value="{{ $key }}" @selected(old('priority', $maintenanceRequest->priority) == $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">القسم</label>
            <select name="department_id" class="form-control">
                <option value="">اختر القسم</option>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" @selected(old('department_id', $maintenanceRequest->department_id) == $department->id)>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-control" required>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(old('status', $maintenanceRequest->status) == $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $maintenanceRequest->notes) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
        <a href="{{ route('maintenance-requests.show', $maintenanceRequest->id) }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection