<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'النظام')</title>

    <style>
        :root {
            --bg: #f4f7fb;
            --sidebar: #1f2937;
            --sidebar-hover: #374151;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: #e5e7eb;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning: #f59e0b;
            --danger: #dc2626;
            --secondary: #6b7280;
            --info: #0ea5e9;
            --shadow: 0 10px 25px rgba(0,0,0,.06);
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
            color: #fff;
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 28px;
            padding: 10px 12px;
            background: rgba(255,255,255,.06);
            border-radius: 14px;
            text-align: center;
        }

        .nav-title {
            font-size: 12px;
            color: #cbd5e1;
            margin: 18px 8px 10px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            text-decoration: none;
            padding: 12px 14px;
            margin-bottom: 8px;
            border-radius: 12px;
            transition: .2s ease;
            font-size: 15px;
        }

        .sidebar a:hover {
            background: var(--sidebar-hover);
        }

        .sidebar a.active {
            background: rgba(37, 99, 235, .95);
        }

        .main {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border);
            padding: 18px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-title h1 {
            margin: 0;
            font-size: 24px;
        }

        .topbar-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .topbar-user {
            color: var(--muted);
            font-size: 14px;
        }

        .page {
            padding: 28px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .page-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary { background: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-warning { background: var(--warning); }
        .btn-danger { background: var(--danger); }
        .btn-secondary { background: var(--secondary); }
        .btn-info { background: var(--info); }
        .btn-success { background: #16a34a; }

        .alert-success {
            background: var(--success-bg);
            color: var(--success-text);
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
            border: 1px solid #bbf7d0;
            font-size: 14px;
        }

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
            font-size: 14px;
            white-space: nowrap;
        }

        tbody td {
            padding: 14px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
            font-size: 14px;
        }

        tbody tr:hover {
            background: #fafcff;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-active { background: #16a34a; }
        .badge-inactive { background: #6b7280; }
        .badge-maintenance { background: #f59e0b; }
        .badge-stopped { background: #dc2626; }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .actions form {
            margin: 0;
        }

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
            font-weight: 700;
            color: #374151;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            background: #fff;
            outline: none;
            transition: .2s ease;
        }

        input:focus, select:focus, textarea:focus {
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

        .muted {
            color: var(--muted);
            font-size: 14px;
        }

        @media (max-width: 900px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .page {
                padding: 18px;
            }

            .topbar {
                padding: 16px 18px;
            }
        }
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
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            min-width: 160px;
            box-shadow: 0 15px 25px rgba(0,0,0,.1);
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
            transition: 0.2s;
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

        .sidebar-toggle {
            width: 100%;
            background: transparent;
            border: 0;
            outline: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            margin: 4px 0;
            border-radius: 12px;
            cursor: pointer;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            text-align: right;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.47);
        }

        .sidebar-toggle span {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-submenu {
            display: none;
            padding-right: 10px;
            margin-bottom: 8px;
        }

        .sidebar-submenu a {
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 10px 14px 10px 18px !important;
            opacity: .95;
        }
        .badge.bg-warning {
            color: #000 !important;
        }

        .badge.bg-success {
            color: #000000 !important;
        }

        .badge.bg-danger {
            color: #000000 !important;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 90px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
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

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="layout">
    @php
        $authUser = auth()->user();
    @endphp
    <div class="sidebar">
        <div class="brand">SAGAS System</div>

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
                <span><i data-lucide="layers-3"></i> أقسام مستقبلية</span>
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

            <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        المراجع
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview">

                    <li class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>الموردين</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('carriers.index') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>الناقلين</p>
                        </a>
                    </li>

                </ul>
            </li>
        </div>    
           

    <div class="main">
        <div class="topbar">
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
</script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
</body>
</html>