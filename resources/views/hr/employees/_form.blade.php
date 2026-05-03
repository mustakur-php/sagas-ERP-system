<div class="form-grid">

    <div class="form-group">
        <label>الاسم بالعربي</label>
        <input type="text" name="name_ar"
            value="{{ old('name_ar', $employee->name_ar ?? '') }}" required>
    </div>

    <div class="form-group">
        <label>الاسم بالإنجليزي</label>
        <input type="text" name="name_en"
            value="{{ old('name_en', $employee->name_en ?? '') }}">
    </div>

    <div class="form-group">
        <label>رقم الموظف</label>
        <input type="text" name="employee_number"
               value="{{ old('employee_number', $employee->employee_number ?? '') }}" required>
    </div>

    <div class="form-group">
        <label>البريد الإلكتروني</label>
        <input type="email" name="email"
               value="{{ old('email', $employee->email ?? '') }}">
    </div>

    <div class="form-group">
        <label>رقم الجوال</label>
        <input type="text" name="mobile"
            value="{{ old('mobile', $employee->mobile ?? '') }}">
    </div>

    <div class="form-group">
        <label>القسم</label>
        <select name="department_id">
            <option value="">-- اختر --</option>
            @foreach($departments as $dep)
                <option value="{{ $dep->id }}"
                    {{ old('department_id', $employee->department_id ?? '') == $dep->id ? 'selected' : '' }}>
                    {{ $dep->name }}{{ $dep->name_ar ?? $dep->name_en ?? '-' }}tion>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>المسمى الوظيفي</label>
        <select name="position_id">
            <option value="">-- اختر --</option>
            @foreach($positions as $pos)
                <option value="{{ $pos->id }}"
                    {{ old('position_id', $employee->position_id ?? '') == $pos->id ? 'selected' : '' }}>
                    {{ $pos->title_ar ?? $pos->title_en ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>موقع العمل</label>
        <select name="work_location_id">
            <option value="">-- اختر موقع العمل --</option>
            @foreach(($workLocations ?? collect()) as $location)
                <option value="{{ $location->id }}"
                    {{ old('work_location_id', $employee->work_location_id ?? '') == $location->id ? 'selected' : '' }}>
                    {{ $location->name }} - نطاق {{ $location->radius_meters }} متر
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>تاريخ التوظيف</label>
        <input type="date" name="hire_date"
               value="{{ old('hire_date', $employee->hire_date ?? '') }}">
    </div>

    <div class="form-group">
        <label>الحالة</label>
        <select name="employment_status" required>
            <option value="active" {{ old('employment_status', $employee->employment_status ?? '') == 'active' ? 'selected' : '' }}>نشط</option>
            <option value="inactive" {{ old('employment_status', $employee->employment_status ?? '') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
            <option value="on_leave" {{ old('employment_status', $employee->employment_status ?? '') == 'on_leave' ? 'selected' : '' }}>إجازة</option>
            <option value="terminated" {{ old('employment_status', $employee->employment_status ?? '') == 'terminated' ? 'selected' : '' }}>منتهي</option>
        </select>
    </div>

    <div class="form-group full">
        <label>ملاحظات</label>
        <textarea name="notes">{{ old('notes', $employee->notes ?? '') }}</textarea>
    </div>

</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">
        حفظ
    </button>

    <a href="{{ route('hr.employees.index') }}" class="btn btn-secondary">
        رجوع
    </a>
</div>