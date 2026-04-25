@extends('layouts.app')

@section('title', 'تعديل محطة')
@section('page_title', 'تعديل المحطة')
@section('page_subtitle', 'تحديث بيانات المحطة الحالية')

@section('content')

<div class="card form-card">
    <form action="{{ route('stations.update', $station->id) }}" method="POST">
    @csrf
    @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>الكود</label>
                <input type="text" name="code" value="{{ old('code', $station->code) }}" placeholder="مثال: ST-001">
                @error('code')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="company_id">الشركة</label>
                <select name="company_id" id="company_id">
                    <option value="">اختر الشركة</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}"
                            @selected(old('company_id', $station->company_id) == $company->id)>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>اسم المحطة</label>
                <input type="text" name="name" value="{{ old('name', $station->name) }}" placeholder="اسم المحطة">
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>الاسم الإنجليزي</label>
                <input type="text" name="name_en" value="{{ old('name_en', $station->name_en) }}" placeholder="English Name">
            </div>

            <div class="form-group">
                <label>المنطقة</label>
                <input type="text" name="region" value="{{ old('region', $station->region) }}" placeholder="المنطقة">
            </div>

            <div class="form-group">
                <label>المدينة</label>
                <input type="text" name="city" value="{{ old('city', $station->city) }}" placeholder="المدينة">
            </div>

            <div class="form-group">
                <label>الحالة</label>
                <select name="status">
                    @foreach(\App\Models\Station::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $station->status) == $key)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group full">
                <h4 style="margin:0;">أسعار الوقود</h4>
            </div>

            @foreach($fuelTypes as $fuel)
                @php
                    $existingPrice = $station->fuelPrices->firstWhere('fuel_type_id', $fuel->id)?->price_per_liter;
                @endphp

                <div class="form-group">
                    <label>{{ $fuel->name }}</label>
                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        name="fuel_prices[{{ $fuel->id }}]"
                        value="{{ old('fuel_prices.' . $fuel->id, $existingPrice) }}"
                        placeholder="أدخل سعر اللتر"
                    >
                </div>
            @endforeach

        <div class="form-group full">
            <h4 style="margin:0;">الخزانات</h4>
        </div>

        @foreach($fuelTypes as $i => $fuel)
            @php
                $tank = $station->tanks->firstWhere('fuel_type_id', $fuel->id);
            @endphp

            <div class="form-group">
                <label>نوع الوقود</label>
                <input type="text" value="{{ $fuel->name }}" readonly>
                <input type="hidden" name="tanks[{{ $i }}][fuel_type_id]" value="{{ $fuel->id }}">
            </div>

            <div class="form-group">
                <label>اسم الخزان</label>
                <input type="text" name="tanks[{{ $i }}][name]" value="{{ $tank->name ?? '' }}">
            </div>

            <div class="form-group">
                <label>السعة</label>
                <input type="number" name="tanks[{{ $i }}][capacity]" value="{{ $tank->capacity ?? '' }}">
            </div>

            <div class="form-group">
                <label>الكمية الحالية</label>
                <input type="number" name="tanks[{{ $i }}][current_quantity]" value="{{ $tank->current_quantity ?? '' }}">
            </div>

            <div class="form-group">
                <label>حد التنبيه</label>
                <input type="number" name="tanks[{{ $i }}][warning_level]" value="{{ $tank->warning_level ?? 30 }}">
            </div>

            <div class="form-group">
                <label>حد الخطر</label>
                <input type="number" name="tanks[{{ $i }}][critical_level]" value="{{ $tank->critical_level ?? 10 }}">
            </div>
        @endforeach

        <div class="form-group full">
            <h4 style="margin:0;">ليات الوقود</h4>
            <p class="muted" style="margin:6px 0 0;">يمكنك تعديل الليات الحالية أو إضافة ليات جديدة.</p>
        </div>

        @for($i = 0; $i < 6; $i++)
            @php
                $existingNozzle = $station->nozzles[$i] ?? null;
            @endphp
            @if($existingNozzle)
                <input type="hidden" name="nozzles[{{ $i }}][id]" value="{{ $existingNozzle->id }}">
            @endif
            <div class="form-group">
                <label>رقم المضخة</label>
                <input
                    type="number"
                    min="1"
                    name="nozzles[{{ $i }}][pump_number]"
                    value="{{ old('nozzles.' . $i . '.pump_number', $existingNozzle?->pump_number) }}"
                    placeholder="مثال: 1"
                >
            </div>

            <div class="form-group">
                <label>رقم اللي</label>
                <input
                    type="number"
                    min="1"
                    name="nozzles[{{ $i }}][nozzle_number]"
                    value="{{ old('nozzles.' . $i . '.nozzle_number', $existingNozzle?->nozzle_number) }}"
                    placeholder="مثال: 1"
                >
            </div>

            <div class="form-group">
                <label>نوع الوقود</label>
                <select name="nozzles[{{ $i }}][fuel_type_id]">
                    <option value="">اختر نوع الوقود</option>
                    @foreach($fuelTypes as $fuel)
                        <option value="{{ $fuel->id }}"
                            @selected(old('nozzles.' . $i . '.fuel_type_id', $existingNozzle?->fuel_type_id) == $fuel->id)>
                            {{ $fuel->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>قراءة العداد الحالية</label>
                <input
                    type="number"
                    step="0.01"
                    min="0"
                    name="nozzles[{{ $i }}][last_meter_reading]"
                    value="{{ old('nozzles.' . $i . '.last_meter_reading', $existingNozzle?->last_meter_reading) }}"
                    placeholder="مثال: 125430.50"
                >
            </div>
        @endfor

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">تحديث</button>
            <a href="{{ route('stations.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </form>
</div>

@endsection