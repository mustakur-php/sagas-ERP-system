@extends('layouts.app')

@section('content')

<div class="page">

    <div class="page-actions">
        <div>
            <h2 class="fw-bold mb-1">الهيكل التنظيمي</h2>
            <p class="text-muted">إدارة الأقسام والمناصب</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row" style="display:flex; gap:20px; flex-wrap:wrap;">

        {{-- إضافة قسم --}}
        <div class="card" style="flex:1; min-width:300px;">
            <h4 class="mb-3">إضافة قسم</h4>

            <form method="POST" action="{{ route('hr.organization.departments.store') }}">
                @csrf

                <div class="form-group">
                    <label>اسم القسم (عربي)</label>
                    <input type="text" name="name_ar" required>
                </div>

                <div class="form-group">
                    <label>اسم القسم (إنجليزي)</label>
                    <input type="text" name="name_en">
                </div>

                <div class="form-group">
                    <label>الكود</label>
                    <input type="text" name="code">
                </div>

                <button class="btn btn-primary mt-2">حفظ</button>
            </form>
        </div>

        {{-- إضافة منصب --}}
        <div class="card" style="flex:1; min-width:300px;">
            <h4 class="mb-3">إضافة منصب</h4>

            <form method="POST" action="{{ route('hr.organization.positions.store') }}">
                @csrf

                <div class="form-group">
                    <label>القسم</label>
                    <select name="department_id" required>
                        <option value="">-- اختر --</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->id }}">
                                {{ $dep->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>اسم المنصب (عربي)</label>
                    <input type="text" name="title_ar" required>
                </div>

                <div class="form-group">
                    <label>اسم المنصب (إنجليزي)</label>
                    <input type="text" name="title_en">
                </div>

                <div class="form-group">
                    <label>الكود</label>
                    <input type="text" name="code">
                </div>

                <button class="btn btn-success mt-2">حفظ</button>
            </form>
        </div>

    </div>

    {{-- عرض الأقسام --}}
    <div class="card mt-4">
        <h4 class="mb-3">الأقسام</h4>

        @if($departments->count())
            <table>
                <thead>
                    <tr>
                        <th>الاسم</th>
                        <th>الكود</th>
                        <th>عدد الموظفين</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($departments as $dep)
                        <tr>
                            <td>{{ $dep->name_ar }}</td>
                            <td>{{ $dep->code }}</td>
                            <td>{{ $dep->employees_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">لا يوجد أقسام</p>
        @endif
    </div>

    {{-- عرض المناصب --}}
    <div class="card mt-4">
        <h4 class="mb-3">المناصب</h4>

        @if($positions->count())
            <table>
                <thead>
                    <tr>
                        <th>المنصب</th>
                        <th>القسم</th>
                        <th>الكود</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($positions as $pos)
                        <tr>
                            <td>{{ $pos->title_ar }}</td>
                            <td>{{ $pos->department->name_ar ?? '-' }}</td>
                            <td>{{ $pos->code }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="muted">لا يوجد مناصب</p>
        @endif
    </div>

</div>

@endsection