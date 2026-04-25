@extends('layouts.app')

@section('title', 'إضافة مورد')
@section('page_title', 'إضافة مورد')
@section('page_subtitle', 'إضافة مورد جديد مع أسعار الوقود')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    :root { --primary-color:#2563eb; --cancel-color:#fef2f2; --cancel-text:#991b1b; --border-soft:#e2e8f0; }
    .modern-rtl-container{direction:rtl;font-family:'Tajawal',sans-serif;max-width:900px;margin:1rem auto;padding:0 20px;}
    .main-card{background:#fff;border-radius:28px;box-shadow:0 20px 40px rgba(0,0,0,.04);border:1px solid var(--border-soft);overflow:hidden;}
    .card-header-soft{padding:1.5rem 2rem;border-bottom:1px solid #f8fafc;}
    .form-section{padding:2rem;}
    .section-label{font-weight:700;font-size:.9rem;color:var(--primary-color);margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;}
    .form-control-soft{width:100%;padding:12px 18px;background:#f8fafc;border:1.5px solid #f1f5f9;border-radius:16px;font-family:'Tajawal',sans-serif;transition:all .2s;}
    .form-control-soft:focus{background:#fff;border-color:var(--primary-color);box-shadow:0 0 0 4px rgba(37,99,235,.06);outline:none;}
    .price-box{background:#f8fafc;border:1.5px solid #f1f5f9;border-radius:18px;padding:16px;}
    .actions-bar{padding:1.5rem 2rem;background:#fcfdfe;display:flex;gap:12px;border-top:1px solid #f8fafc;}
    .btn-modern{padding:12px 30px;border-radius:16px;font-weight:700;font-family:'Tajawal',sans-serif;border:none;transition:.3s;cursor:pointer;}
    .btn-save{background:var(--primary-color);color:white;}
    .btn-cancel{background:var(--cancel-color);color:var(--cancel-text);text-decoration:none;}
</style>

<div class="modern-rtl-container">
    <div class="main-card">
        <div class="card-header-soft">
            <h4 class="fw-bold mb-1">إضافة مورد جديد</h4>   
        </div>

        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <div class="form-section">
                <div class="section-label">● البيانات الأساسية</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">اسم المورد</label>
                        <input type="text" name="name" class="form-control-soft" value="{{ old('name') }}" placeholder="اسم المورد" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control-soft" value="{{ old('phone') }}" placeholder="05xxxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">الشخص المسؤول</label>
                        <input type="text" name="contact_person" class="form-control-soft" value="{{ old('contact_person') }}" placeholder="اسم المسؤول">
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">الحالة</label>
                        <select name="is_active" class="form-control-soft">
                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="small fw-bold mb-2 d-block">ملاحظات</label>
                        <textarea name="notes" class="form-control-soft" rows="3" placeholder="ملاحظات اختيارية">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="row mt-3 supplier-prices">

                    <div class="col-md-4">
                        <label class="text-success fw-bold">بنزين 91</label>
                        <input type="number" step="0.01" name="prices[1]"
                            class="form-control border-success text-success fw-bold">
                    </div>

                    <div class="col-md-4">
                        <label class="text-danger fw-bold">بنزين 95</label>
                        <input type="number" step="0.01" name="prices[2]"
                            class="form-control border-danger text-danger fw-bold">
                    </div>

                    <div class="col-md-4">
                        <label class="text-warning fw-bold">ديزل</label>
                        <input type="number" step="0.01" name="prices[3]"
                            class="form-control border-warning text-warning fw-bold">
                    </div>

                </div>
            </div>

            <div class="actions-bar">
                <button type="submit" class="btn-modern btn-save">حفظ المورد</button>
                <a href="{{ route('suppliers.index') }}" class="btn-modern btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@endsection
