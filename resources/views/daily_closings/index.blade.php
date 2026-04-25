@extends('layouts.app')

@section('content')
<div class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
        <h1 style="margin:0;">الإغلاقات اليومية</h1>
        <a href="{{ route('daily_closings.create') }}" class="btn btn-primary">إضافة إغلاق جديد</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('daily_closings.index') }}" style="margin-bottom: 20px;">
        <div style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
            <div>
                <label>المحطة</label><br>
                <select name="station_id" class="form-control">
                    <option value="">الكل</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->id }}" {{ request('station_id') == $station->id ? 'selected' : '' }}>
                            {{ $station->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>التاريخ</label><br>
                <input type="date" name="closing_date" class="form-control" value="{{ request('closing_date') }}">
            </div>

            <div>
                <button type="submit" class="btn btn-dark">فلترة</button>
            </div>
        </div>
    </form>

    <div style="overflow-x:auto;">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المحطة</th>
                    <th>التاريخ</th>
                    <th>ملاحظات</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($closings as $closing)
                    <tr>
                        <td>{{ $closing->id }}</td>
                        <td>{{ $closing->station->name ?? '-' }}</td>
                        <td>{{ $closing->closing_date }}</td>
                        <td>{{ $closing->notes ?: '-' }}</td>
                        <td>
                            <a href="{{ route('daily_closings.show', $closing->id) }}" class="btn btn-sm btn-info">
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;">لا توجد بيانات</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $closings->links() }}
    </div>
</div>
@endsection