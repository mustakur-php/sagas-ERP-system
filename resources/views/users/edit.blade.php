@extends('layouts.app')

@section('content')
<div class="container">
    <h2>تعديل مستخدم</h2>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul style="margin:0; padding-right:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>الاسم</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control">
        </div>

        <div class="mb-3">
            <label>الإيميل</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control">
        </div>

        <div class="mb-3">
            <label>رقم الجوال</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="form-control">
        </div>

        <div class="mb-3">
            <label>كلمة المرور الجديدة</label>
            <input type="password" name="password" class="form-control">
            <small class="text-muted">اتركها فارغة إذا ما تبي تغير كلمة المرور</small>
        </div>

        <div class="mb-3">
            <label>الشركة</label>
            <select name="company_id" class="form-control" required>
                <option value="">اختر الشركة</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('company_id', $user->company_id) == $company->id)>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3" id="station-field-wrapper">
            <label>المحطة</label>
            <select name="station_id" class="form-control">
                <option value="">اختر المحطة</option>
                @foreach($stations as $station)
                    <option value="{{ $station->id }}" @selected(old('station_id', $user->station_id) == $station->id)>
                        {{ $station->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>الدور</label>
            <select name="roles[]" class="form-control">
                <option value="">اختر الدور</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">تحديث</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.querySelector('[name="roles[]"]');
    const stationField = document.getElementById('station-field-wrapper');

    function toggleStation() {
        const selected = roleSelect.options[roleSelect.selectedIndex].text;

        if (selected.includes('مشرف')) {
            stationField.style.display = 'block';
        } else {
            stationField.style.display = 'none';
        }
    }

    roleSelect.addEventListener('change', toggleStation);
    toggleStation();
});
</script>