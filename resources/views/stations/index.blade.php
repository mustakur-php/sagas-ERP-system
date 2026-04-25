@extends('layouts.app')

@section('title', 'المحطات')
@section('page_title', 'قائمة المحطات')
@section('page_subtitle', 'إدارة بيانات المحطات وحالاتها التشغيلية')

@section('content')

<div class="page-actions">
    <div class="muted">يمكنك من هنا إضافة المحطات وتعديل حالتها.</div>

    <a href="{{ route('stations.create') }}" class="btn btn-primary">
        + إضافة محطة جديدة
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الكود</th>
                    <th>الشركة</th>
                    <th>الاسم</th>
                    <th>المدينة</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stations as $station)
                    <tr>
                        <td>{{ $station->id }}</td>
                        <td>{{ $station->code }}</td>
                        <td>{{ $station->company->name ?? '-' }}</td>
                        <td>{{ $station->name }}</td>
                        <td>{{ $station->city ?? '-' }}</td>
                        
                        <td>
                            <span class="badge 
                                @if($station->status == 'active') badge-active
                                @elseif($station->status == 'inactive') badge-inactive
                                @elseif($station->status == 'under_maintenance') badge-maintenance
                                @elseif($station->status == 'stopped') badge-stopped
                                @endif
                            ">
                                {{ \App\Models\Station::STATUSES[$station->status] ?? $station->status }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">

                                <a href="{{ route('stations.edit', $station->id) }}" class="btn btn-warning">
                                    تعديل
                                </a>

                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle">
                                        إجراءات ▼
                                    </button>

                                    <div class="dropdown-menu">

                                        <form action="{{ route('stations.changeStatus', [$station->id, 'active']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">✔ تفعيل</button>
                                        </form>

                                        <form action="{{ route('stations.changeStatus', [$station->id, 'inactive']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">⛔ تعطيل</button>
                                        </form>

                                        <form action="{{ route('stations.changeStatus', [$station->id, 'under_maintenance']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">🛠 صيانة</button>
                                        </form>

                                        <form action="{{ route('stations.changeStatus', [$station->id, 'stopped']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item danger">⛔ إيقاف</button>
                                        </form>

                                    </div>
                                </div>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">لا توجد محطات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection