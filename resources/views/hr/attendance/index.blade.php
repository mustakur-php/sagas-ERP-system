@extends('layouts.app')

@section('content')

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">الحضور والانصراف</h2>
            <p class="text-muted">تسجيل الحضور والانصراف حسب نطاق موقع العمل</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-success" style="background:#fee2e2;color:#991b1b;border-color:#fecaca;">
            <ul style="margin:0;padding-right:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:grid; grid-template-columns: 380px 1fr; gap:20px; align-items:start;">

        <div class="card">
            <h4 style="margin-top:0;">تسجيل حركة</h4>

            <div class="form-group">
                <label>الموظف</label>
                <select id="employee_id" required>
                    <option value="">-- اختر الموظف --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->name_ar ?? $employee->name_en ?? '-' }}
                            @if($employee->workLocation)
                                - {{ $employee->workLocation->name }}
                            @else
                                - بدون موقع
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" id="latitude">
            <input type="hidden" id="longitude">

            <div class="form-actions">
                <form id="checkInForm" method="POST" action="{{ route('hr.attendance.check-in') }}">
                    @csrf
                    <input type="hidden" name="employee_id" class="form_employee_id">
                    <input type="hidden" name="latitude" class="form_latitude">
                    <input type="hidden" name="longitude" class="form_longitude">
                    <button type="button" class="btn btn-success" onclick="submitAttendance('checkInForm')">
                        تسجيل حضور
                    </button>
                </form>

                <form id="checkOutForm" method="POST" action="{{ route('hr.attendance.check-out') }}">
                    @csrf
                    <input type="hidden" name="employee_id" class="form_employee_id">
                    <input type="hidden" name="latitude" class="form_latitude">
                    <input type="hidden" name="longitude" class="form_longitude">
                    <button type="button" class="btn btn-warning" onclick="submitAttendance('checkOutForm')">
                        تسجيل انصراف
                    </button>
                </form>
            </div>

            <p id="locationStatus" class="muted" style="margin-top:15px;">
                عند الضغط سيتم طلب موقعك من المتصفح.
            </p>
        </div>

        <div class="card">
            <h4 style="margin-top:0;">حركات اليوم</h4>

            @if($todayRecords->count())
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>الحضور</th>
                                <th>مسافة الحضور</th>
                                <th>الانصراف</th>
                                <th>مسافة الانصراف</th>
                                <th>الساعات</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayRecords as $record)
                                <tr>
                                    <td>{{ $record->employee->name_ar ?? $record->employee->name_en ?? '-' }}</td>
                                    <td>{{ $record->check_in ?? '-' }}</td>
                                    <td>
                                        {{ $record->check_in_distance_meters !== null ? $record->check_in_distance_meters . ' متر' : '-' }}
                                    </td>
                                    <td>{{ $record->check_out ?? '-' }}</td>
                                    <td>
                                        {{ $record->check_out_distance_meters !== null ? $record->check_out_distance_meters . ' متر' : '-' }}
                                    </td>
                                    <td>{{ $record->worked_hours ?? '-' }}</td>
                                    <td>
                                        <span class="status-badge status-approved">
                                            {{ $record->status ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="muted">لا توجد حركات حضور اليوم.</p>
            @endif
        </div>

    </div>
</div>

<script>
function submitAttendance(formId) {
    const employeeId = document.getElementById('employee_id').value;
    const statusBox = document.getElementById('locationStatus');

    if (!employeeId) {
        alert('اختر الموظف أولًا');
        return;
    }

    if (!navigator.geolocation) {
        alert('المتصفح لا يدعم تحديد الموقع');
        return;
    }

    statusBox.innerText = 'جاري تحديد موقعك...';

    navigator.geolocation.getCurrentPosition(
        function (position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            document.querySelectorAll('.form_employee_id').forEach(el => el.value = employeeId);
            document.querySelectorAll('.form_latitude').forEach(el => el.value = latitude);
            document.querySelectorAll('.form_longitude').forEach(el => el.value = longitude);

            statusBox.innerText = 'تم تحديد الموقع، جاري تسجيل الحركة...';

            document.getElementById(formId).submit();
        },
        function (error) {
            let message = 'تعذر الحصول على الموقع.';

            if (error.code === error.PERMISSION_DENIED) {
                message = 'تم رفض صلاحية الموقع من المتصفح.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                message = 'معلومات الموقع غير متاحة.';
            } else if (error.code === error.TIMEOUT) {
                message = 'انتهت مهلة تحديد الموقع.';
            }

            statusBox.innerText = message;
            alert(message);
        },
        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }
    );
}
</script>

@endsection