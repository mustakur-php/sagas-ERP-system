@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>إضافة عقد جديد</h3>

        <a href="{{ route('hr.contracts.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>يوجد أخطاء في البيانات:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.contracts.store') }}">
        @csrf

        <div class="card mb-3">
            <div class="card-header">
                بيانات العقد
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">الموظف</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">اختر الموظف</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                    {{ $employee->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">رقم العقد</label>
                        <input type="text" name="contract_number" class="form-control"
                               value="{{ old('contract_number') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">نوع العقد</label>
                        <select name="contract_type" class="form-control" required>
                            <option value="full_time" @selected(old('contract_type') == 'full_time')>دوام كامل</option>
                            <option value="part_time" @selected(old('contract_type') == 'part_time')>دوام جزئي</option>
                            <option value="temporary" @selected(old('contract_type') == 'temporary')>مؤقت</option>
                            <option value="probation" @selected(old('contract_type') == 'probation')>تجربة</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control" required>
                            <option value="active" @selected(old('status') == 'active')>نشط</option>
                            <option value="expired" @selected(old('status') == 'expired')>منتهي</option>
                            <option value="renewed" @selected(old('status') == 'renewed')>مجدد</option>
                            <option value="terminated" @selected(old('status') == 'terminated')>منهي</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تاريخ البداية</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{ old('start_date') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تاريخ النهاية</label>
                        <input type="date" name="end_date" class="form-control"
                               value="{{ old('end_date') }}">
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                بيانات الراتب والبدلات
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label class="form-label">الراتب الأساسي</label>
                        <input type="number" step="0.01" min="0" name="basic_salary"
                               class="form-control salary-field"
                               value="{{ old('basic_salary', 0) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">بدل السكن</label>
                        <input type="number" step="0.01" min="0" name="housing_allowance"
                               class="form-control salary-field"
                               value="{{ old('housing_allowance', 0) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">بدل النقل</label>
                        <input type="number" step="0.01" min="0" name="transport_allowance"
                               class="form-control salary-field"
                               value="{{ old('transport_allowance', 0) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">بدلات أخرى</label>
                        <input type="number" step="0.01" min="0" name="other_allowances"
                               class="form-control salary-field"
                               value="{{ old('other_allowances', 0) }}">
                    </div>

                    <div class="col-md-12">
                        <div class="alert alert-info mb-0">
                            إجمالي الراتب:
                            <strong id="salaryTotal">0.00</strong>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                ملاحظات
            </div>

            <div class="card-body">
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">
            حفظ العقد
        </button>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fields = document.querySelectorAll('.salary-field');
    const totalEl = document.getElementById('salaryTotal');

    function calculateTotal() {
        let total = 0;

        fields.forEach(function (field) {
            total += parseFloat(field.value) || 0;
        });

        totalEl.textContent = total.toFixed(2);
    }

    fields.forEach(function (field) {
        field.addEventListener('input', calculateTotal);
    });

    calculateTotal();
});
</script>
@endsection