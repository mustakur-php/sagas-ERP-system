@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>المستخدمون</h2>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            إضافة مستخدم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @php
        $roleNames = [
            'super_admin' => 'مدير النظام',
            'company_admin' => 'مدير الشركة',
            'station_supervisor' => 'مشرف محطة',
            'maintenance_manager' => 'مدير الصيانة',
            'technician' => 'فني',
            'finance' => 'المالية',
            'operations' => 'التشغيل',
            'transport' => 'النقل',
        ];
    @endphp

    <div class="card">
        <div class="card-header">
            قائمة المستخدمين
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الجوال</th>
                        <th>الشركة</th>
                        <th>المحطة</th>
                        <th>الأدوار</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $user->name }}</td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->phone ?? '-' }}</td>

                            <td>{{ optional($user->company)->name ?? '-' }}</td>

                            <td>{{ optional($user->station)->name ?? '-' }}</td>

                            <td>
                                @if($user->roles && $user->roles->count())
                                    {{ $user->roles->map(fn($role) => $roleNames[$role->slug] ?? $role->name)->join('، ') }}
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    تعديل
                                </a>

                                <form action="{{ route('users.destroy', $user->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">لا يوجد مستخدمون</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($users, 'links'))
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection