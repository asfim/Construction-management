<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('erp.app_name'))@if(isset($currentCompany)) — {{ $currentCompany->name }}@endif</title>

    <!-- Google Fonts: Hind Siliguri (Bangla) & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
            --topbar-bg: #ffffff;
            --body-bg: #f8fafc;
            --text-primary: #1e293b;
            --text-muted: #64748b;
            --brand-primary: #2563eb;
            --brand-accent: #0284c7;
        }

        body {
            font-family: 'Hind Siliguri', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 270px;
            background-color: var(--sidebar-bg);
            color: #cbd5e1;
            height: 100vh;
            max-height: 100vh;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            box-shadow: 2px 0 12px rgba(0,0,0,0.1);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            scrollbar-width: thin;
            scrollbar-color: #334155 #0b1120;
        }

        /* Sleek Modern Scrollbar for Sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #0b1120;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            background: linear-gradient(135deg, rgba(37,99,235,0.2) 0%, rgba(15,23,42,0.95) 100%);
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(8px);
            flex-shrink: 0;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #2563eb, #0284c7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
            flex-shrink: 0;
        }

        .sidebar-nav {
            padding: 1rem 0 5rem;
            flex-grow: 1;
        }

        .nav-category {
            padding: 0.75rem 1.5rem 0.35rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 700;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.7rem 1.5rem;
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-item-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .nav-item-link.active {
            color: #ffffff;
            background-color: rgba(37,99,235,0.15);
            border-left-color: var(--brand-primary);
            font-weight: 600;
        }

        .nav-item-link i {
            font-size: 1.15rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Topbar */
        .topbar {
            height: 70px;
            background-color: var(--topbar-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .topbar-selector-form {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .tenant-select {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            font-size: 0.88rem;
            font-weight: 500;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
            max-width: 240px;
            color: #1e293b;
        }

        .tenant-select:focus {
            background-color: #ffffff;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }

        /* Page Content */
        .content-body {
            padding: 2rem;
            flex: 1;
        }

        /* Premium Cards */
        .card-custom {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }

        /* Status Badges */
        .badge-status {
            font-weight: 600;
            font-size: 0.8rem;
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
        }
        .badge-active { background-color: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-planning { background-color: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
        .badge-on_hold { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .badge-completed { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        /* Universal Bootstrap 5 Pagination Styling */
        .pagination {
            margin-bottom: 0;
            gap: 4px;
        }
        .pagination .page-item .page-link {
            color: #1e3a8a;
            border-color: #e2e8f0;
            padding: 0.38rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }
        .pagination .page-item .page-link:hover {
            background-color: #f1f5f9;
            color: #1d4ed8;
            border-color: #cbd5e1;
        }
        .pagination .page-item.active .page-link {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.25);
        }
        .pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }
        /* Guard against any unstyled inline pagination SVGs scaling out of proportion */
        nav svg,
        .pagination svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        /* Footer */
        .footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>

    <style id="erp-premium-theme">
        :root {
            --brand-primary: #4f46e5;
            --brand-accent: #0ea5e9;
            --brand-gradient: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
            --sidebar-bg: #0b1220;
            --sidebar-hover: rgba(255,255,255,0.06);
            --body-bg: #f1f5f9;
            --surface: #ffffff;
            --border-soft: #e5eaf2;
            --shadow-sm: 0 1px 2px rgba(15,23,42,.04), 0 1px 3px rgba(15,23,42,.06);
            --shadow-md: 0 4px 6px -1px rgba(15,23,42,.06), 0 10px 24px -6px rgba(15,23,42,.10);
            --shadow-lg: 0 20px 40px -12px rgba(79,70,229,.25);
            --radius: 14px;
        }

        body {
            background:
                radial-gradient(900px 400px at 100% -10%, rgba(79,70,229,.08), transparent 60%),
                radial-gradient(700px 360px at -10% 0%, rgba(14,165,233,.07), transparent 60%),
                var(--body-bg);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            background: linear-gradient(180deg, #0b1220 0%, #101a30 100%);
            border-right: 1px solid rgba(255,255,255,.05);
        }
        .sidebar-brand-icon { background: var(--brand-gradient); box-shadow: 0 6px 16px rgba(79,70,229,.45); }
        .nav-category {
            margin-top: .6rem;
            color: #64748b;
            display: flex; align-items: center; gap: .5rem;
        }
        .nav-category::after { content: ""; flex: 1; height: 1px; background: rgba(255,255,255,.06); }
        .nav-item-link {
            margin: 2px .75rem;
            padding: .62rem .9rem;
            border-radius: 10px;
            border-left: none;
            position: relative;
            color: #aab4c5;
        }
        .nav-item-link:hover { transform: translateX(3px); color: #fff; background: var(--sidebar-hover); }
        .nav-item-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(79,70,229,.55), rgba(14,165,233,.25));
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.08), 0 6px 14px rgba(79,70,229,.25);
        }
        .nav-item-link.active::before {
            content: ""; position: absolute; left: -.75rem; top: 20%; bottom: 20%;
            width: 4px; border-radius: 0 4px 4px 0; background: #38bdf8;
        }
        .nav-item-link i { color: #7f8db0; transition: color .2s; }
        .nav-item-link:hover i, .nav-item-link.active i { color: #7dd3fc; }

        /* ---------- Topbar (glass) ---------- */
        .topbar {
            background: rgba(255,255,255,.78);
            backdrop-filter: saturate(180%) blur(14px);
            -webkit-backdrop-filter: saturate(180%) blur(14px);
            border-bottom: 1px solid var(--border-soft);
        }
        .tenant-select { border-radius: 10px; background-color: #f8fafc; border-color: var(--border-soft); }

        /* ---------- Page entrance ---------- */
        .content-body { animation: pageIn .3s ease-out; }
        @keyframes pageIn { from { opacity: 0; } to { opacity: 1; } }

        /* Ensure Bootstrap modals always stack above modal-backdrop */
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
        }

        /* ---------- Headings ---------- */
        .content-body h4.fw-bold, .content-body h3.fw-bold {
            letter-spacing: -.01em;
            background: linear-gradient(90deg, #0f172a, #334155);
            -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* ---------- Cards ---------- */
        .card-custom, .content-body .card {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius);
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .card-custom:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
        .card-header { background: linear-gradient(180deg,#fff,#f8fafc) !important; border-bottom: 1px solid var(--border-soft); }
        .card-footer { border-top: 1px solid var(--border-soft); }

        /* ---------- Tables ---------- */
        .table { --bs-table-hover-bg: rgba(79,70,229,.045); margin-bottom: 0; }
        .table > thead.table-light > tr > th,
        .table > thead > tr > th {
            background: linear-gradient(180deg,#f8fafc,#f1f5f9) !important;
            color: #475569;
            font-size: .74rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .06em;
            border-bottom: 1px solid var(--border-soft);
            padding-top: .85rem; padding-bottom: .85rem;
            white-space: nowrap;
        }
        .table > tbody > tr > td { padding-top: .85rem; padding-bottom: .85rem; border-color: #eef2f7; }
        .table-hover > tbody > tr { transition: background .15s ease, transform .15s ease; }
        .table-bordered { border-color: var(--border-soft); }
        .table-bordered > :not(caption) > * > * { border-color: var(--border-soft); }

        /* ---------- Buttons ---------- */
        .btn { border-radius: 10px; font-weight: 600; transition: all .2s ease; }
        .btn-primary {
            background: var(--brand-gradient); border: none;
            box-shadow: 0 6px 16px rgba(79,70,229,.30);
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--brand-gradient); filter: brightness(1.07);
            transform: translateY(-1px); box-shadow: var(--shadow-lg);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline-primary { color: var(--brand-primary); border-color: rgba(79,70,229,.35); }
        .btn-outline-primary:hover { background: var(--brand-gradient); border-color: transparent; color: #fff; transform: translateY(-1px); }
        .btn-outline-secondary, .btn-light { border-color: var(--border-soft); }
        .btn-success { box-shadow: 0 6px 14px rgba(25,135,84,.25); }
        .btn-warning { box-shadow: 0 6px 14px rgba(255,193,7,.30); }
        .btn-sm { border-radius: 8px; }

        /* ---------- Forms ---------- */
        .form-control, .form-select {
            border-radius: 10px; border: 1px solid #d9e0ec; background-color: #fbfcfe;
            padding-top: .55rem; padding-bottom: .55rem;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .form-control-sm, .form-select-sm { padding-top: .38rem; padding-bottom: .38rem; }
        .form-control:focus, .form-select:focus {
            background-color: #fff; border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(79,70,229,.14);
        }
        .form-label { color: #334155; font-size: .88rem; }
        .form-check-input:checked { background-color: var(--brand-primary); border-color: var(--brand-primary); }
        .input-group > .btn { border-radius: 0 10px 10px 0; }

        /* ---------- Badges ---------- */
        .badge { border-radius: 8px; font-weight: 600; letter-spacing: .01em; }
        .badge.rounded-pill { border-radius: 50rem; }

        /* ---------- Alerts ---------- */
        .alert { border-radius: 12px; border: none; box-shadow: var(--shadow-sm); }
        .alert-success { background: linear-gradient(90deg,#ecfdf5,#f0fdf9); color: #065f46; border-left: 4px solid #10b981; }
        .alert-danger  { background: linear-gradient(90deg,#fef2f2,#fff5f5); color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-info    { background: linear-gradient(90deg,#eff6ff,#f0f9ff); color: #1e40af; border-left: 4px solid #3b82f6; }

        /* ---------- Pagination ---------- */
        .pagination .page-link { border-radius: 8px; margin: 0 2px; border: 1px solid var(--border-soft); color: #475569; }
        .pagination .active > .page-link { background: var(--brand-gradient); border-color: transparent; color: #fff; }

        /* ---------- Dropdown ---------- */
        .dropdown-menu { border-radius: 12px; border: 1px solid var(--border-soft); box-shadow: var(--shadow-md); padding: .4rem; }
        .dropdown-item { border-radius: 8px; }

        /* ---------- Scrollbar (main) ---------- */
        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ---------- Footer ---------- */
        .footer { background: transparent; border-top: 1px solid var(--border-soft); }

        /* ---------- Print: keep documents clean ---------- */
        @media print {
            body { background: #fff !important; }
            .sidebar, .topbar, .footer, .d-print-none, .alert { display: none !important; }
            .main-wrapper { margin-left: 0 !important; }
            .content-body { padding: 0 !important; animation: none; }
            .card-custom, .content-body .card { box-shadow: none !important; border: 1px solid #ddd; }
            .content-body h4.fw-bold, .content-body h3.fw-bold { -webkit-text-fill-color: #000; background: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>

    <style id="erp-color-theme">
        :root {
            --c-indigo: #4f46e5; --c-indigo-2: #7c3aed;
            --c-emerald: #059669; --c-emerald-2: #10b981;
            --c-amber: #d97706;  --c-amber-2: #f59e0b;
            --c-sky: #0284c7;    --c-sky-2: #06b6d4;
            --c-rose: #e11d48;   --c-rose-2: #fb7185;
        }

        /* Richer, colourful page background */
        body {
            background:
                radial-gradient(900px 480px at 100% -5%, rgba(124,58,237,.14), transparent 60%),
                radial-gradient(800px 420px at -5% 5%, rgba(6,182,212,.13), transparent 60%),
                radial-gradient(700px 400px at 50% 110%, rgba(16,185,129,.10), transparent 60%),
                #eef2fb !important;
            background-attachment: fixed;
        }

        /* ---------- Dashboard KPI cards: vivid gradients ---------- */
        .kpi-card { position: relative; color: #fff !important; border: none !important; overflow: hidden; }
        .kpi-card::after {
            content: ""; position: absolute; right: -30px; bottom: -40px;
            width: 140px; height: 140px; border-radius: 50%;
            background: rgba(255,255,255,.14); pointer-events: none;
        }
        .kpi-card::before {
            content: ""; position: absolute; right: 30px; top: -50px;
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(255,255,255,.10); pointer-events: none;
        }
        .kpi-card .text-muted, .kpi-card .text-dark, .kpi-card .text-success { color: rgba(255,255,255,.92) !important; }
        .kpi-card h3, .kpi-card h4 { color: #fff !important; -webkit-text-fill-color: #fff !important; background: none !important; }
        .kpi-card [class*="-subtle"] { background: rgba(255,255,255,.22) !important; color: #fff !important; backdrop-filter: blur(4px); }
        .kpi-card:hover { transform: translateY(-4px); }
        .kpi-indigo  { background: linear-gradient(135deg, #4f46e5, #7c3aed) !important; box-shadow: 0 14px 28px -10px rgba(79,70,229,.55) !important; }
        .kpi-emerald { background: linear-gradient(135deg, #059669, #10b981) !important; box-shadow: 0 14px 28px -10px rgba(5,150,105,.55) !important; }
        .kpi-amber   { background: linear-gradient(135deg, #ea580c, #f59e0b) !important; box-shadow: 0 14px 28px -10px rgba(234,88,12,.55) !important; }
        .kpi-sky     { background: linear-gradient(135deg, #0284c7, #06b6d4) !important; box-shadow: 0 14px 28px -10px rgba(2,132,199,.55) !important; }

        /* ---------- Stat / summary cards on ALL other pages: pastel tint, cycling colours ---------- */
        .row > [class*="col-"]:nth-child(4n+1) > .card-custom:not(.kpi-card):not(:has(.table)) { background: linear-gradient(135deg, #eef2ff, #e0e7ff); border-color: #c7d2fe; border-left: 5px solid var(--c-indigo); }
        .row > [class*="col-"]:nth-child(4n+2) > .card-custom:not(.kpi-card):not(:has(.table)) { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-color: #a7f3d0; border-left: 5px solid var(--c-emerald-2); }
        .row > [class*="col-"]:nth-child(4n+3) > .card-custom:not(.kpi-card):not(:has(.table)) { background: linear-gradient(135deg, #fffbeb, #fef3c7); border-color: #fde68a; border-left: 5px solid var(--c-amber-2); }
        .row > [class*="col-"]:nth-child(4n+4) > .card-custom:not(.kpi-card):not(:has(.table)) { background: linear-gradient(135deg, #ecfeff, #cffafe); border-color: #a5f3fc; border-left: 5px solid var(--c-sky-2); }
        /* Large form/detail cards stay clean white but get a coloured top accent */
        .col-lg-8 > .card-custom, .col-lg-10 > .card-custom, .col-lg-11 > .card-custom {
            background: #fff !important; border-top: 4px solid var(--c-indigo) !important; border-left: 1px solid var(--border-soft) !important;
        }

        /* ---------- Coloured card headers ---------- */
        .card-header {
            background: linear-gradient(90deg, #eef2ff 0%, #f5f3ff 50%, #ecfeff 100%) !important;
            border-bottom: 1px solid #dbe2f5 !important;
        }
        .card-header h5, .card-header h6 { color: #312e81; }

        /* ---------- Tinted table headers + zebra ---------- */
        .table > thead > tr > th {
            background: linear-gradient(180deg, #eef2ff, #e0e7ff) !important;
            color: #3730a3 !important;
            border-bottom: 2px solid #c7d2fe !important;
        }
        .table > tbody > tr:nth-child(even) > td { background-color: rgba(238,242,255,.45); }
        .table-hover > tbody > tr:hover > td { background-color: rgba(99,102,241,.10) !important; }
        .table > tfoot td { background: #f5f3ff !important; }
        .table-primary, .table-primary > td { background: linear-gradient(90deg,#e0e7ff,#ede9fe) !important; color: #3730a3; }

        /* ---------- Coloured, friendlier badges ---------- */
        .bg-success-subtle { background: #d1fae5 !important; color: #047857 !important; border-color: #6ee7b7 !important; }
        .bg-warning-subtle { background: #fef3c7 !important; color: #b45309 !important; border-color: #fcd34d !important; }
        .bg-danger-subtle  { background: #ffe4e6 !important; color: #be123c !important; border-color: #fda4af !important; }
        .bg-info-subtle    { background: #cffafe !important; color: #0e7490 !important; border-color: #67e8f9 !important; }
        .bg-primary-subtle { background: #e0e7ff !important; color: #4338ca !important; border-color: #a5b4fc !important; }
        .bg-secondary-subtle { background: #e2e8f0 !important; color: #475569 !important; }

        /* ---------- Section divider headings inside forms ---------- */
        h5.fw-bold.border-bottom, h5.fw-bold.mb-3.border-bottom {
            color: #3730a3; border-bottom: 2px solid transparent !important;
            border-image: linear-gradient(90deg, #4f46e5, #06b6d4, transparent) 1; padding-bottom: .5rem !important;
        }

        /* ---------- Sidebar: richer colour accents ---------- */
        .sidebar { background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 60%, #172554 100%) !important; }
        .nav-category { color: #818cf8; }

        /* ---------- Topbar tint ---------- */
        .topbar { background: linear-gradient(90deg, rgba(255,255,255,.88), rgba(238,242,255,.88)); }

        /* ---------- Colourful action buttons ---------- */
        .btn-success { background: linear-gradient(135deg,#059669,#10b981); border: none; }
        .btn-warning { background: linear-gradient(135deg,#f59e0b,#fbbf24); border: none; color: #422006; }
        .btn-info    { background: linear-gradient(135deg,#0284c7,#06b6d4); border: none; }
        .btn-danger  { background: linear-gradient(135deg,#e11d48,#fb7185); border: none; }
        .btn-success:hover, .btn-warning:hover, .btn-info:hover, .btn-danger:hover { filter: brightness(1.08); transform: translateY(-1px); }

        /* ---------- Progress bars ---------- */
        .progress { background: #e0e7ff; border-radius: 20px; }
        .progress-bar { background: linear-gradient(90deg, #4f46e5, #06b6d4) !important; border-radius: 20px; }

        /* ---------- Ultra-Clean Executive Print Styling ---------- */
        @page {
            size: auto;
            margin: 0mm;
        }

        @media print {
            html, body { 
                background: #ffffff !important; 
                margin: 0 !important;
            }
            body { 
                padding: 12mm 15mm !important;
                color: #0f172a !important; 
                font-size: 10.5pt !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important;
            }
            .sidebar, .topbar, .footer, .d-print-none, .alert, .btn, .modal, .toast-container, .pagination { 
                display: none !important; 
            }
            .main-wrapper { 
                margin: 0 !important; 
                padding: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
            }
            .content-body { 
                padding: 0 !important; 
                animation: none !important; 
                width: 100% !important; 
                margin: 0 !important; 
            }
            .container, .container-fluid { 
                max-width: 100% !important; 
                padding: 0 !important; 
                margin: 0 !important; 
            }
            .card-custom, .content-body .card { 
                background: #ffffff !important; 
                box-shadow: none !important; 
                border: 1px solid #cbd5e1 !important; 
                border-radius: 4px !important;
                page-break-inside: auto;
            }
            .card-header {
                background: #f8fafc !important;
                border-bottom: 1px solid #cbd5e1 !important;
                color: #0f172a !important;
            }
            .card-header h5, .card-header h6 {
                color: #0f172a !important;
            }
            .table { 
                border-color: #cbd5e1 !important; 
                page-break-inside: auto;
            }
            .table > thead > tr > th { 
                background: #f1f5f9 !important; 
                color: #0f172a !important; 
                border: 1px solid #cbd5e1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 0.82rem;
            }
            .table > tbody > tr > td { 
                border: 1px solid #e2e8f0 !important; 
                background: transparent !important;
                font-size: 0.85rem;
            }
            .table > tfoot > tr > td {
                border: 1px solid #cbd5e1 !important;
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            tr { page-break-inside: avoid !important; }
            .print-document-header { page-break-after: avoid !important; }
            .print-signatures-row { page-break-inside: avoid !important; }
            .print-hide-icon { display: none !important; }
            .col-print-7 { width: 58.33333333% !important; flex: 0 0 auto !important; }
            .col-print-5 { width: 41.66666667% !important; flex: 0 0 auto !important; }
            .text-print-end { text-align: right !important; }
            .badge {
                border: 1px solid #94a3b8 !important;
                color: #0f172a !important;
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
            }
            .print-company-logo {
                max-height: 70px !important;
                max-width: 150px !important;
                object-fit: contain !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebarMenu">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-building-gear"></i>
            </div>
            <div>
                <h6 class="mb-0 text-white fw-bold">{{ __('erp.app_name') }}</h6>
                <small class="text-secondary" style="font-size: 0.75rem;">H Tech Provision IT</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-category">{{ __('erp.nav.dashboard') }}</div>
            <a href="{{ route('dashboard') }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>{{ __('erp.nav.dashboard') }}</span>
            </a>

            <div class="nav-category">{{ __('erp.nav.projects') }}</div>
            <a href="{{ route('projects.index') }}" class="nav-item-link {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                <i class="bi bi-kanban"></i>
                <span>{{ __('erp.nav.project_list') }}</span>
            </a>
            @can('project.create')
            <a href="{{ route('projects.create') }}" class="nav-item-link {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>{{ __('erp.nav.new_project') }}</span>
            </a>
            @endcan
            @can('branch.view')
            <a href="{{ route('branches.index') }}" class="nav-item-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Associated Branches</span>
            </a>
            @endcan
            @can('user.view')
            <a href="{{ route('users.index', ['role' => 'Project Manager']) }}" class="nav-item-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Project Managers & Staff</span>
            </a>
            @endcan

            @if(isset($currentProject) && $currentProject)
            <div class="nav-category">Project Workspace ({{ Str::limit($currentProject->name, 16) }})</div>
            <a href="{{ route('projects.boqs.index', $currentProject) }}" class="nav-item-link {{ request()->routeIs('projects.boqs.*') || request()->routeIs('boq-items.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>{{ __('erp.nav.boq_estimation') }}</span>
            </a>
            <a href="{{ route('projects.budget.show', $currentProject) }}" class="nav-item-link {{ request()->routeIs('projects.budget.*') ? 'active' : '' }}">
                <i class="bi bi-pie-chart"></i>
                <span>{{ __('erp.nav.budget_cost') }}</span>
            </a>
            @endif

            <div class="nav-category">Site Execution & Billing</div>
            @can('dpr.view')
            <a href="{{ route('dpr.index') }}" class="nav-item-link {{ request()->routeIs('dpr.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i>
                <span>Daily Progress (DPR)</span>
            </a>
            @endcan
            @can('bill.view')
            <a href="{{ route('client-bills.index') }}" class="nav-item-link {{ request()->routeIs('client-bills.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Client Billing (RA Bills)</span>
            </a>
            @endcan
            @can('budget.view')
            <a href="{{ route('project-expenses.index') }}" class="nav-item-link {{ request()->routeIs('project-expenses.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i>
                <span>Project Expenses</span>
            </a>
            @endcan

            <div class="nav-category">Procurement & Inventory</div>
            @can('mr.view')
            <a href="{{ route('material-requisitions.index') }}" class="nav-item-link {{ request()->routeIs('material-requisitions.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Material Requisitions (MR)</span>
            </a>
            @endcan
            @can('po.view')
            <a href="{{ route('purchase-orders.index') }}" class="nav-item-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>Purchase Orders (PO)</span>
            </a>
            @endcan
            @can('grn.view')
            <a href="{{ route('goods-receipts.index') }}" class="nav-item-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Goods Receipts (GRN)</span>
            </a>
            @endcan
            @can('stock.view')
            <a href="{{ route('inventory.ledger') }}" class="nav-item-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i>
                <span>Stock Ledger & Balance</span>
            </a>
            @endcan
            @can('warehouse.view')
            <a href="{{ route('warehouses.index') }}" class="nav-item-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>Warehouses & Stores</span>
            </a>
            @endcan

            <div class="nav-category">Materials & Master Data</div>
            @can('item.view')
            <a href="{{ route('items.index') }}" class="nav-item-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>{{ __('erp.nav.items') }}</span>
            </a>
            <a href="{{ route('item-categories.index') }}" class="nav-item-link {{ request()->routeIs('item-categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>Item Categories</span>
            </a>
            <a href="{{ route('units.index') }}" class="nav-item-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>
                <span>Units of Measurement (UoM)</span>
            </a>
            @endcan
            @can('supplier.view')
            <a href="{{ route('suppliers.index') }}" class="nav-item-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i>
                <span>{{ __('erp.nav.suppliers') }}</span>
            </a>
            @endcan
            @can('cost-code.view')
            <a href="{{ route('cost-codes.index') }}" class="nav-item-link {{ request()->routeIs('cost-codes.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-2"></i>
                <span>Cost Codes & WBS</span>
            </a>
            @endcan

            <div class="nav-category">{{ __('erp.nav.organizations') }}</div>
            @can('company.view')
            <a href="{{ route('companies.index') }}" class="nav-item-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                <i class="bi bi-building-gear"></i>
                <span>Company Settings</span>
            </a>
            @endcan
            @can('branch.view')
            <a href="{{ route('branches.index') }}" class="nav-item-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>Branches</span>
            </a>
            @endcan
            @can('user.view')
            <a href="{{ route('users.index') }}" class="nav-item-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Users & Staff</span>
            </a>
            @endcan
            @can('role.view')
            <a href="{{ route('roles.index') }}" class="nav-item-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>Roles & Permissions</span>
            </a>
            @endcan
            @can('audit.view')
            <a href="{{ route('audit-logs.index') }}" class="nav-item-link {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>{{ __('erp.nav.audit_logs') }}</span>
            </a>
            @endcan
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-secondary d-lg-none" type="button" id="sidebarToggle" aria-label="Toggle Navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <!-- Single Company Brand Display -->
                @if(isset($currentCompany))
                <a href="{{ route('companies.edit', $currentCompany) }}" class="d-none d-md-flex align-items-center text-decoration-none text-muted small pe-2 py-1 px-2 rounded border bg-light-subtle" title="Click to edit Company Settings">
                    @if($currentCompany->logo_url)
                        <img src="{{ $currentCompany->logo_url }}" alt="{{ $currentCompany->name }}" class="me-2 rounded bg-white" style="height: 22px; max-width: 65px; object-fit: contain;">
                    @else
                        <i class="bi bi-building text-primary me-2"></i>
                    @endif
                    <strong class="text-dark">{{ $currentCompany->name }}</strong>
                </a>
                @endif

                <!-- Active Project Selector -->
                @if(isset($userProjects) && $userProjects->count() > 0)
                <form action="{{ route('project.switch') }}" method="POST" class="d-none d-md-flex align-items-center ms-2">
                    @csrf
                    <i class="bi bi-kanban text-success me-2"></i>
                    <select name="project_id" class="form-select form-select-sm tenant-select" onchange="this.form.submit()">
                        <option value="all">{{ __('erp.all_projects') }}</option>
                        @foreach($userProjects as $proj)
                            <option value="{{ $proj->id }}" {{ (isset($currentProject) && $currentProject->id == $proj->id) ? 'selected' : '' }}>
                                {{ $proj->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>

            <!-- Right Controls: Language, Notifications & User Profile -->
            <div class="d-flex align-items-center gap-3">
                <!-- Language Badge -->
                <div class="d-none d-sm-flex align-items-center">
                    <span class="badge bg-light text-secondary border px-2 py-1">
                        <i class="bi bi-globe2 me-1 text-primary"></i> EN
                    </span>
                </div>

                <!-- User Profile Dropdown -->
                @auth
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 600;">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="text-start d-none d-sm-block">
                            <div class="fw-semibold text-dark small leading-tight">{{ auth()->user()->name }}</div>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">
                                {{ auth()->user()->roles->first()?->name ?? 'User' }}
                            </span>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted d-block">{{ auth()->user()->email }}</small>
                            <small class="text-primary fw-medium">{{ auth()->user()->designation ?? 'Team Member' }}</small>
                        </li>
                        @if(isset($currentCompany))
                            @can('company.manage')
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('companies.edit', $currentCompany) }}">
                                    <i class="bi bi-gear-fill text-primary"></i>
                                    <span>Company Settings</span>
                                </a>
                            </li>
                            @endcan
                        @endif
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>{{ __('erp.nav.logout') }}</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-4 mb-0 border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-4 mb-0 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show m-4 mb-0 border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show m-4 mb-0 border-0 shadow-sm" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Page Body -->
        <main class="content-body">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div>
                © {{ date('Y') }} <strong>H Tech Provision IT</strong>. {{ __('erp.tagline') }}
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('erp.bangladesh') }}
                </span>
                <span class="text-muted small">
                    <i class="bi bi-clock me-1"></i> Dhaka Time: {{ now()->timezone('Asia/Dhaka')->format('d-m-Y H:i') }}
                </span>
            </div>
        </footer>
    </div>

    @stack('modals')

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebarMenu')?.classList.toggle('show');
        });

        // Ensure all Bootstrap modals are appended directly to body to prevent stacking context or backdrop occlusion issues
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.modal').forEach(function (modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });
        });

        document.addEventListener('show.bs.modal', function (event) {
            if (event.target && event.target.parentElement !== document.body) {
                document.body.appendChild(event.target);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
