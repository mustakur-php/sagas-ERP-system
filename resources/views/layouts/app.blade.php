<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'النظام')</title>

<style>
/* =========================================================
   1) ROOT VARIABLES
========================================================= */
:root {
    --bg: #f4f7fb;
    --sidebar: #111827;
    --sidebar-2: #1f2937;
    --sidebar-hover: rgba(255,255,255,.08);

    --card: #ffffff;
    --text: #111827;
    --muted: #6b7280;
    --border: #e5e7eb;

    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
    --secondary: #6b7280;
    --info: #0ea5e9;

    --success-bg: #dcfce7;
    --success-text: #166534;

    --radius-sm: 10px;
    --radius: 16px;
    --radius-lg: 22px;

    --shadow-sm: 0 1px 2px rgba(15,23,42,.04);
    --shadow: 0 8px 24px rgba(15,23,42,.06);
    --shadow-lg: 0 18px 40px rgba(15,23,42,.10);
}


/* =========================================================
   2) GLOBAL RESET
========================================================= */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Tahoma, Arial, sans-serif;
    background: var(--bg);
    color: var(--text);
    direction: rtl;
}

a {
    text-decoration: none;
}

.layout {
    display: flex;
    min-height: 100vh;
}


/* =========================================================
   3) SIDEBAR
========================================================= */
.sidebar {
    width: 260px;
    background: linear-gradient(180deg, var(--sidebar-2) 0%, var(--sidebar) 100%);
    color: #fff;
    padding: 18px 14px;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,.25) transparent;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,.25);
    border-radius: 999px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,.4);
}

.brand {
    font-size: 21px;
    font-weight: 800;
    margin-bottom: 18px;
    padding: 12px;
    background: rgba(255,255,255,.07);
    border-radius: 14px;
    text-align: center;
    letter-spacing: .3px;
}

.nav-title {
    font-size: 11px;
    color: #94a3b8;
    margin: 18px 8px 8px;
    font-weight: 800;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #e5e7eb;
    padding: 10px 12px;
    margin-bottom: 5px;
    border-radius: 11px;
    transition: .2s ease;
    font-size: 14px;
    font-weight: 600;
}

.sidebar a i {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.sidebar a:hover {
    background: var(--sidebar-hover);
    color: #fff;
}

.sidebar a.active {
    background: rgba(37, 99, 235, .95);
    color: #fff;
    box-shadow: 0 8px 18px rgba(37, 99, 235, .25);
}


/* =========================================================
   4) SIDEBAR DROPDOWN
========================================================= */
.sidebar-group {
    margin-bottom: 5px;
}

.sidebar-toggle {
    width: 100%;
    background: transparent;
    border: 0;
    outline: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 12px;
    margin: 4px 0 5px;
    border-radius: 11px;
    cursor: pointer;
    color: #e5e7eb;
    font-size: 14px;
    font-weight: 800;
    text-align: right;
    transition: .2s ease;
}

.sidebar-toggle:hover {
    background: var(--sidebar-hover);
    color: #fff;
}

.sidebar-toggle span {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar-toggle span i {
    font-size: 17px;
    width: 20px;
    text-align: center;
}

.sidebar-toggle .toggle-arrow {
    font-size: 13px;
    transition: .2s ease;
    opacity: .75;
}

.sidebar-group.open .toggle-arrow {
    transform: rotate(-90deg);
}

.sidebar-submenu {
    display: none;
    padding-right: 12px;
    margin: 2px 0 8px;
    border-right: 1px solid rgba(255,255,255,.10);
}

.sidebar-group.open .sidebar-submenu {
    display: block;
}

.sidebar-submenu a {
    font-size: 13px !important;
    font-weight: 500 !important;
    padding: 9px 12px !important;
    margin-bottom: 3px !important;
    color: #cbd5e1;
}

.sidebar-submenu a:hover {
    color: #fff;
    background: rgba(255,255,255,.07);
}


/* =========================================================
   5) MAIN AREA + TOPBAR
========================================================= */
.main {
    flex: 1;
    min-width: 0;
}

.topbar {
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    padding: 18px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 20;
}

.topbar-title h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
}

.topbar-title p {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 14px;
}

.topbar-user {
    color: var(--muted);
    font-size: 14px;
    font-weight: 600;
}

.page {
    padding: 26px;
}


/* =========================================================
   6) CARDS
========================================================= */
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 22px;
}

.clean-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.clean-card-header {
    padding: 18px 20px;
    border-bottom: 1px solid var(--border);
    background: #fbfdff;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}


/* =========================================================
   7) PAGE ACTIONS
========================================================= */
.page-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}


/* =========================================================
   8) BUTTONS
========================================================= */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    color: #fff;
    font-size: 14px;
    font-weight: 800;
    transition: .2s ease;
    gap: 6px;
}

.btn:hover {
    transform: translateY(-1px);
    filter: brightness(.97);
}

.btn-primary { background: var(--primary); }
.btn-primary:hover { background: var(--primary-dark); }

.btn-warning { background: var(--warning); color:#111827; }
.btn-danger { background: var(--danger); }
.btn-secondary { background: var(--secondary); }
.btn-info { background: var(--info); }
.btn-success { background: var(--success); }

.btn-light {
    background: #f8fafc;
    color: #334155;
    border: 1px solid var(--border);
}

.btn-white {
    background: #fff;
    color: #334155;
    border: 1px solid var(--border);
}


/* =========================================================
   9) ALERTS
========================================================= */
.alert-success {
    background: var(--success-bg);
    color: var(--success-text);
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 18px;
    border: 1px solid #bbf7d0;
    font-size: 14px;
}


/* =========================================================
   10) TABLES
========================================================= */
.table-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead th {
    text-align: right;
    background: #f9fafb;
    color: #374151;
    border-bottom: 1px solid var(--border);
    padding: 14px 12px;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
}

tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 13px;
}

tbody tr:hover {
    background: #fafcff;
}

.table td,
.table th {
    vertical-align: middle;
}


/* =========================================================
   11) BADGES + STATUSES
========================================================= */
.badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 999px;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
}

.badge-active { background: #16a34a; }
.badge-inactive { background: #6b7280; }
.badge-maintenance { background: #f59e0b; }
.badge-stopped { background: #dc2626; }

.badge.bg-warning,
.badge.bg-success,
.badge.bg-danger {
    color: #000 !important;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 90px;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
    border: 1px solid transparent;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
    border-color: #bbf7d0;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
    border-color: #fecaca;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}

.status-partial {
    background: #dbeafe;
    color: #1d4ed8;
    border-color: #bfdbfe;
}

.status-received {
    background: #ccfbf1;
    color: #0f766e;
    border-color: #99f6e4;
}


/* =========================================================
   12) FORMS
========================================================= */
.form-card {
    max-width: 850px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-size: 14px;
    font-weight: 800;
    color: #374151;
}

input,
select,
textarea {
    width: 100%;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 14px;
    background: #fff;
    outline: none;
    transition: .2s ease;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
}

.error {
    color: #dc2626;
    font-size: 13px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
}


/* =========================================================
   13) ACTIONS + MINI CONTROLS
========================================================= */
.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.actions form {
    margin: 0;
}

.item-actions-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: nowrap;
}

.inline-item-form {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
}

.mini-input {
    width: 110px;
    height: 34px;
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    background: #fff;
    color: #111827;
    outline: none;
}

.mini-input::placeholder {
    color: #6b7280;
}

.mini-btn {
    height: 34px;
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 8px;
    white-space: nowrap;
}


/* =========================================================
   14) DROPDOWN MENU
========================================================= */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: #6b7280;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 110%;
    right: 0;
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    min-width: 160px;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    z-index: 50;
}

.dropdown-item {
    width: 100%;
    padding: 12px 14px;
    border: none;
    background: none;
    text-align: right;
    cursor: pointer;
    font-size: 14px;
    transition: .2s;
}

.dropdown-item:hover {
    background: #f3f4f6;
}

.dropdown-item.danger:hover {
    background: #fee2e2;
    color: #b91c1c;
}

.dropdown:hover .dropdown-menu {
    display: block;
}


/* =========================================================
   15) UTILITIES
========================================================= */
.muted {
    color: var(--muted);
    font-size: 14px;
}

.text-muted {
    color: var(--muted) !important;
}

.rounded-soft {
    border-radius: var(--radius);
}

/* =========================================================
   MOBILE MENU BUTTON + OVERLAY
========================================================= */
.menu-btn {
    display: none;
    font-size: 24px;
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--text);
}

.mobile-overlay {
    display: none;
}
/* =========================================================
   16) RESPONSIVE
========================================================= */
@media (max-width: 900px) {
    .layout {
        flex-direction: column;
    }

    .sidebar {
        position: fixed;
        right: -280px;
        top: 0;
        width: 260px;
        height: 100vh;
        max-height: none;
        z-index: 1000;
        transition: right .3s ease;
    }

    .sidebar.open {
        right: 0;
    }

    .mobile-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 900;
        display: none;
    }

    .mobile-overlay.show {
        display: block;
    }

    .menu-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .topbar > div:last-child {
        width: 100%;
        flex-wrap: wrap;
    }

    .topbar form {
        width: 100%;
    }

    .topbar form select {
        width: 100%;
    }

    .topbar .btn {
        width: 100%;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .page {
        padding: 18px;
    }

    .topbar {
        padding: 16px 18px;
        align-items: flex-start;
        gap: 12px;
        flex-direction: column;
    }
}
</style>
</head>
<body>

<div class="layout">
    <div class="mobile-overlay" onclick="toggleMobileSidebar()"></div>
    @php
        $authUser = auth()->user();
    @endphp
    <div class="sidebar">
        <div class="brand">SAGAS ERP System</div>

            <a href="{{ route('dashboard') }}">
                <i data-lucide="layout-dashboard"></i>
                لوحة التحكم
            </a>

            <a href="{{ route('dashboard.stations') }}">
                <i data-lucide="layout-list"></i>
                داشبورد المحطات
            </a>

            
            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('basic-menu')">
                <span><i data-lucide="building-2"></i> الإدارة الأساسية</span>
                <span>▾</span>
            </button>

            <div id="basic-menu" class="sidebar-submenu">
                <a href="{{ route('companies.index') }}">
                    <i data-lucide="building-2"></i>
                    الشركات
                </a>

                <a href="{{ route('stations.index') }}">
                    <i data-lucide="fuel"></i>
                    المحطات
                </a>

                <a href="#">
                    <i data-lucide="droplet"></i>
                    أنواع الوقود
                </a>

                <a href="{{ route('users.index') }}">
                    <i data-lucide="users"></i>
                    المستخدمين
                </a>
            </div>

            

            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('ops-menu')">
                <span><i data-lucide="briefcase"></i> التشغيل</span>
                <span>▾</span>
            </button>

            <div id="ops-menu" class="sidebar-submenu">
                <a href="{{ route('fuel-orders.index') }}">
                    <i data-lucide="clipboard-list"></i>
                    طلبات الوقود
                </a>

                <a href="{{ route('fuel-orders.create') }}">
                    <i data-lucide="file-plus-2"></i>
                    طلب وقود جديد
                </a>

                <a href="{{ route('fuel-orders.receiving') }}">
                    <i data-lucide="truck"></i>
                    استلام الوقود
                </a>

                <a href="{{ route('daily_closings.index') }}">
                    <i data-lucide="calendar-check"></i>
                    الإغلاقات اليومية
                </a>

                <a href="{{ route('maintenance-requests.index') }}">
                    <i data-lucide="wrench"></i>
                    الصيانة
                </a>

                <a href="#">
                    <i data-lucide="alert-circle"></i>
                    البلاغات
                </a>
            </div>

            
            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('sales-menu')">
                <span><i data-lucide="bar-chart-3"></i> المبيعات</span>
                <span>▾</span>
            </button>

            <div id="sales-menu" class="sidebar-submenu">
                <a href="{{ route('sales.index') }}">
                    <i data-lucide="bar-chart-3"></i>
                    المبيعات
                </a>
            </div>

            
            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('admin-menu')">
                <span><i data-lucide="shield"></i> الإدارة</span>
                <span>▾</span>
            </button>

            <div id="admin-menu" class="sidebar-submenu">
                <a href="#">
                    <i data-lucide="shield"></i>
                    الأدوار
                </a>

                <a href="#">
                    <i data-lucide="key-round"></i>
                    الصلاحيات
                </a>
            </div>

            

            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('future-menu')">
                <span><i data-lucide="book"></i> أقسام مستقبلية</span>
                <span>▾</span>
            </button>

            <div id="future-menu" class="sidebar-submenu">
                <a href="#">
                    <i data-lucide="wallet"></i>
                    المالية
                </a>
                <a href="{{ route('fuel-orders.finance') }}">
                    مراجعة المالية
                </a>
                <a href="#">
                    <i data-lucide="truck"></i>
                    النقل
                </a>
            </div>

            
            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('references-menu')">
                <span><i data-lucide="layers-3"></i> المراجع </span>
                <span>▾</span>
            </button>
            
            <div id="references-menu" class="sidebar-submenu">
                <a href="{{ route('suppliers.index') }}">
                    <i data-lucide="clipboard-list"></i>
                     الموردين
                </a>

                <a href="{{ route('carriers.index') }}">
                    <i data-lucide="file-plus-2"></i>
                     الناقلين
                </a>
            </div>

            <button type="button" class="sidebar-toggle" onclick="toggleSidebarMenu('HR-menu')">
                <span><i data-lucide="layers-3"></i> الموارد البشريه </span>
                <span>▾</span>
            </button>

            <div id="HR-menu" class="sidebar-submenu">

                <a href="{{ route('hr.index') }}">
                    <i class="bi bi-speedometer2"></i>
                    لوحة التحكم
                </a>

                <a href="{{ route('hr.employees.index') }}">
                    <i class="bi bi-person-lines-fill"></i>
                    الموظفين
                </a>

                <a href="{{ route('hr.attendance.index') }}">
                    <i class="bi bi-clock-history"></i>
                    الحضور والانصراف
                </a>

                <a href="{{ route('hr.payroll.index') }}">
                    <i class="bi bi-wallet2"></i>
                    الرواتب
                </a>

                <a href="{{ route('hr.organization.index') }}">
                    <i class="bi bi-diagram-3"></i>
                    الهيكل التنظيمي
                </a>

                <a href="{{ route('hr.work-locations.index') }}">
                    <i class="bi bi-geo-alt"></i>
                    مواقع العمل
                </a>
                
                <a href="{{ route('hr.settings.index') }}">
                    <i class="bi bi-gear"></i>
                    الإعدادات
                </a>
            </div>
        </div>    

        

    <div class="main">
        <div class="topbar">
            <button type="button" class="menu-btn" onclick="toggleMobileSidebar()">
                ☰
            </button>
            <div class="topbar-title">
            <h1>@yield('page_title', 'النظام')</h1>
            <p>@yield('page_subtitle', 'لوحة عمل النظام')</p>
            
        </div>

        <div style="display:flex; align-items:center; gap:12px;">

            <!-- فلتر الشركات -->
            <form method="GET" action="{{ route('set.company') }}">
                <select name="company_id" onchange="this.form.submit()">
                    <option value="">كل الشركات</option>

                    @foreach(\App\Models\Company::all() as $company)
                        <option value="{{ $company->id }}"
                            {{ session('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach

                </select>
            </form>
            @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">تسجيل خروج</button>
                </form>
            @endauth
            <div class="topbar-user">
                {{ auth()->user()->name ?? '---' }}
            </div>

        </div>
    </div>

        <div class="page">
            @yield('content')
        </div>
    </div>
</div>
<script>
    function toggleSidebarMenu(menuId) {
        const menus = document.querySelectorAll('.sidebar-submenu');

        menus.forEach(menu => {
            if (menu.id === menuId) {
                menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
            } else {
                menu.style.display = 'none';
            }
        });
    }
    function toggleMobileSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.mobile-overlay').classList.toggle('show');
    }
</script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
</body>
</html>