<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #f1f5f9;
        --cancel-color: #fef2f2;
        --cancel-text: #991b1b;
        --text-dark: #1e293b;
        --border-soft: #e2e8f0;
    }

    .modern-rtl-container {
        direction: rtl;
        font-family: 'Tajawal', sans-serif;
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 20px;
    }

    .main-card {
        background: #ffffff;
        border-radius: 28px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.04);
        border: 1px solid var(--border-soft);
        overflow: hidden;
    }

    .card-header-soft {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f8fafc;
    }

    .form-section { padding: 2rem; }

    .section-label {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--primary-color);
        margin-bottom: 1.2rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control-soft {
        width: 100%;
        padding: 12px 18px;
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 16px;
        font-family: 'Tajawal', sans-serif;
        transition: all 0.2s;
    }

    .form-control-soft:focus {
        background: #fff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.06);
        outline: none;
    }

    /* أنيميشن بسيط لظهور المحطة */
    #station_group {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .roles-flex {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .role-item input { display: none; }

    .role-tag {
        display: block;
        padding: 10px 20px;
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 14px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: 0.2s;
    }

    .role-item input:checked + .role-tag {
        background: #eff6ff;
        border-color: var(--primary-color);
        color: var(--primary-color);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.1);
    }

    .actions-bar {
        padding: 1.5rem 2rem;
        background: #fcfdfe;
        display: flex;
        gap: 12px;
        border-top: 1px solid #f8fafc;
    }

    .btn-modern {
        padding: 12px 30px;
        border-radius: 16px;
        font-weight: 700;
        font-family: 'Tajawal', sans-serif;
        border: none;
        transition: 0.3s;
        cursor: pointer;
    }

    .btn-save { background: var(--primary-color); color: white; }
    .btn-cancel { 
        background: var(--cancel-color); 
        color: var(--cancel-text); 
        text-decoration: none; 
    }
</style>

<div class="modern-rtl-container">
    <div class="main-card">
        <div class="card-header-soft">
            <h4 class="fw-bold mb-1">إضافة مستخدم جديد</h4>
            <p class="text-muted small mb-0">أدخل البيانات لربط المستخدم بالشركة والأدوار</p>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-section">
                <div class="section-label">● البيانات الأساسية</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control-soft" placeholder="الاسم" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control-soft" placeholder="email@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">الشركة</label>
                        <select name="company_id" id="company_id" class="form-control-soft" required>
                            <option value="">اختر الشركة...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="small fw-bold mb-2 d-block">رقم الجوال</label>
                        <input type="text" name="phone" class="form-control-soft" placeholder="05xxxxxxxx">
                    </div>
                </div>

                <div id="station_group" class="mb-4" style="display: none;">
                    <div class="p-3 border-0 rounded-4" style="background-color: #f0f7ff;">
                        <label class="small fw-bold mb-2 d-block text-primary">المحطة المرتبطة</label>
                        <select name="station_id" id="station_id" class="form-control-soft border-white">
                            <option value="">اختر المحطة...</option>
                            @foreach($stations as $station)
                                <option value="{{ $station->id }}" data-company-id="{{ $station->company_id }}">
                                    {{ $station->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-2 d-block">يرجى تحديد المحطة لهذا المشرف</small>
                    </div>
                </div>

                <div class="section-label">● الصلاحيات</div>
                <div class="roles-flex mb-4">
                    @foreach($roles as $role)
                        <label class="role-item">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" data-slug="{{ $role->slug }}">
                            <span class="role-tag">{{ $roleNames[$role->slug] ?? $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="section-label">● الأمان</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="password" name="password" class="form-control-soft" placeholder="كلمة المرور">
                    </div>
                    <div class="col-md-6">
                        <input type="password" name="password_confirmation" class="form-control-soft" placeholder="تأكيد الكلمة">
                    </div>
                </div>
            </div>

            <div class="actions-bar">
                <button type="submit" class="btn-modern btn-save">حفظ المستخدم</button>
                <a href="{{ route('users.index') }}" class="btn-modern btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
    const stationGroup = document.getElementById('station_group');
    const companySelect = document.getElementById('company_id');
    const stationSelect = document.getElementById('station_id');

    function checkSupervisor() {
        // التحقق إذا كان "مشرف محطة" مختاراً بناءً على الـ Slug
        const isSupervisor = Array.from(roleCheckboxes).some(input => 
            input.checked && input.dataset.slug === 'station_supervisor'
        );
        
        if(isSupervisor) {
            stationGroup.style.display = 'block';
            // إضافة حركة بسيطة
            stationGroup.style.opacity = '0';
            setTimeout(() => { stationGroup.style.opacity = '1'; }, 10);
        } else {
            stationGroup.style.display = 'none';
            stationSelect.value = '';
        }
    }

    function filterStations() {
        const selectedCompany = companySelect.value;
        Array.from(stationSelect.options).forEach(option => {
            if (!option.value) return;
            option.hidden = (option.dataset.companyId !== selectedCompany);
        });
        if (stationSelect.selectedOptions[0]?.hidden) stationSelect.value = '';
    }

    roleCheckboxes.forEach(input => input.addEventListener('change', checkSupervisor));
    companySelect.addEventListener('change', filterStations);

    // تشغيل الفلتر عند التحميل في حال وجود قيم قديمة (Old values)
    checkSupervisor();
    filterStations();
});
</script>