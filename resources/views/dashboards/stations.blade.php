

@extends('layouts.app')

@section('title', 'لوحة المحطات')
@section('page_title', 'لوحة المحطات')
@section('page_subtitle', 'ملخص سريع لحالة المحطات والخزانات')

@section('content')
<style>
    .stations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
    }

    .flip-card {
        perspective: 1000px;
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 420px;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        cursor: pointer;
    }

    .flip-card.is-flipped .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-face {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: 14px;
    }

    .flip-card-back {
        transform: rotateY(180deg);
    }

    .flip-card-content {
        height: 100%;
        padding: 15px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .flip-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding-left: 4px;
    }

    .flip-scroll-area::-webkit-scrollbar {
        width: 6px;
    }

    .flip-scroll-area::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 999px;
    }

    .flip-scroll-area::-webkit-scrollbar-track {
        background: transparent;
    }

    .tank-progress {
        height: 10px;
        background: #e5e7eb;
        border-radius: 999px;
        overflow: hidden;
    }

    .tank-progress-bar {
        height: 100%;
    }

    .flip-note {
        margin-top: auto;
        padding-top: 10px;
        font-size: 12px;
        color: #6b7280;
    }

    .sales-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 6px;
        font-size: 13px;
    }

    .closing-box {
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 14px;
        font-size: 13px;
        font-weight: bold;
    }
</style>

<div class="page-actions" style="margin-bottom:20px;">
    <div class="muted">
        هنا تشوف حالة المحطات والخزانات بشكل سريع.
    </div>
</div>
<div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
    <div class="card" style="padding:15px; min-width:220px; border-right:5px solid #dc2626;">
        <div class="muted">محطات حرجة</div>
        <h2 style="margin:8px 0 0;">{{ $criticalStationsCount }}</h2>
    </div>

    <div class="card" style="padding:15px; min-width:220px; border-right:5px solid #f59e0b;">
        <div class="muted">محطات تحتاج متابعة</div>
        <h2 style="margin:8px 0 0;">{{ $warningStationsCount }}</h2>
    </div>
</div>
<div class="stations-grid">
    @forelse($stations as $station)
        @php
            $stationBadgeColor = match($station->dashboard_status) {
                'critical' => '#dc2626',
                'warning' => '#f59e0b',
                default => '#16a34a',
            };

            $stationBadgeText = match($station->dashboard_status) {
                'critical' => 'حالة حرجة',
                'warning' => 'تحتاج متابعة',
                default => 'طبيعية',
            };

            $closingColor = match($station->closing_status) {
                'done' => '#16a34a',
                'warning' => '#f59e0b',
                default => '#dc2626',
            };

            $closingText = match($station->closing_status) {
                'done' => 'تم الإغلاق اليومي',
                'warning' => 'إغلاق ناقص',
                default => 'لم يتم الإغلاق',
            };
        @endphp

        <div class="flip-card" onclick="this.classList.toggle('is-flipped')">
            <div class="flip-card-inner">

                {{-- الوجه الأمامي --}}
                <div class="flip-card-face flip-card-front">
                    <div class="card flip-card-content" style="border-right:5px solid {{ $stationBadgeColor }};">
                        <h3 style="margin-top:0;">{{ $station->name }}</h3>

                        <div style="margin-bottom:10px;">
                            <span style="
                                display:inline-block;
                                padding:6px 10px;
                                border-radius:999px;
                                background: {{ $stationBadgeColor }};
                                color:#fff;
                                font-size:13px;
                            ">
                                {{ $stationBadgeText }}
                            </span>
                        </div>

                        <div class="muted" style="margin-bottom:10px;">
                            {{ $station->company->name ?? 'بدون شركة' }}
                        </div>

                        <hr>

                        @if($station->dashboard_status === 'critical')
                            <div style="background:#fee2e2; color:#991b1b; padding:10px; border-radius:8px; margin-bottom:12px;">
                                يوجد خزان أو أكثر في مستوى حرج ويحتاج تدخل سريع.
                            </div>
                        @elseif($station->dashboard_status === 'warning')
                            <div style="background:#fef3c7; color:#92400e; padding:10px; border-radius:8px; margin-bottom:12px;">
                                يوجد خزان أو أكثر في مستوى تحذيري ويحتاج متابعة.
                            </div>
                        @endif

                        <div class="flip-scroll-area">
                        @forelse($station->tanks as $tank)
                            @php
                                $color = match($tank->level_status) {
                                    'critical' => '#dc2626',
                                    'warning' => '#f59e0b',
                                    default => '#16a34a',
                                };
                            @endphp

                            <div style="margin-bottom:14px;">
                                <div style="display:flex; justify-content:space-between; gap:10px; margin-bottom:6px;">
                                    <strong>{{ $tank->fuelType->name ?? '-' }}</strong>
                                    <span style="color: {{ $color }};">{{ number_format($tank->fill_percentage, 1) }}%</span>
                                </div>

                                <div class="muted" style="margin-bottom:6px;">
                                    الكمية الحالية: {{ number_format($tank->current_quantity, 2) }} لتر
                                </div>

                                <div class="muted" style="margin-bottom:6px;">
                                    السعة: {{ number_format($tank->capacity, 2) }} لتر
                                </div>

                                <div class="muted" style="margin-bottom:8px;">
                                    الفاضي: {{ number_format($tank->available_space, 2) }} لتر
                                </div>

                                <div class="tank-progress">
                                    <div
                                        class="tank-progress-bar"
                                        style="width: {{ min($tank->fill_percentage, 100) }}%; background: {{ $color }};"
                                    ></div>
                                </div>
                            </div>
                        @empty
                            <div class="muted">لا توجد خزانات لهذه المحطة</div>
                        @endforelse
                        </div>

                        <div class="flip-note">
                            اضغط على الكرت لعرض ملخص الإغلاق والمبيعات
                        </div>
                    </div>
                </div>

                {{-- الوجه الخلفي --}}
                <div class="flip-card-face flip-card-back">
                    <div class="card flip-card-content" style="border-right:5px solid {{ $closingColor }};">
                        <h3 style="margin-top:0;">{{ $station->name }}</h3>

                        <div class="closing-box" style="background: {{ $closingColor }}20; color: {{ $closingColor }};">
                            {{ $closingText }}
                        </div>

                        <h4 style="margin:0 0 10px;">مبيعات اليوم</h4>

                        @if(!empty($station->today_sales))
                            @php
                                $totalAmount = collect($station->today_sales)->sum('amount');
                            @endphp

                            <div class="flip-scroll-area">
                                @foreach($station->today_sales as $sale)
                                    <div style="display:flex; justify-content:space-between; gap:10px; margin-bottom:10px;">
                                        <div>
                                            <strong>{{ $sale['fuel'] }}</strong>
                                        </div>

                                        <div style="text-align:left;">
                                            <div><strong>{{ number_format($sale['liters'], 0) }} لتر</strong></div>
                                            <div style="font-size:12px; color:#6b7280;">
                                                {{ number_format($sale['amount'], 2) }} ريال
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div style="
                                    margin-top:12px;
                                    padding-top:10px;
                                    border-top:1px solid #e5e7eb;
                                    font-weight:bold;
                                ">
                                    إجمالي: {{ number_format($totalAmount, 2) }} ريال
                                </div>
                            </div>
                        @else
                            <div class="muted" style="margin-bottom:12px;">
                                لا توجد بيانات مبيعات اليوم
                            </div>
                        @endif

                        <div class="flip-note">
                            اضغط على الكرت للرجوع إلى واجهة الخزانات
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="padding:15px;">
            لا توجد محطات لعرضها
        </div>
    @endforelse
</div>
@endsection