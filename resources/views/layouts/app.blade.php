<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('erp.app_name')) — H Tech Provision IT</title>

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

            @if(isset($currentProject) && $currentProject)
            <div class="nav-category">প্রকল্প কার্যক্রম ({{ Str::limit($currentProject->name, 16) }})</div>
            <a href="{{ route('projects.boqs.index', $currentProject) }}" class="nav-item-link {{ request()->routeIs('projects.boqs.*') || request()->routeIs('boq-items.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                <span>{{ __('erp.nav.boq_estimation') }}</span>
            </a>
            <a href="{{ route('projects.budget.show', $currentProject) }}" class="nav-item-link {{ request()->routeIs('projects.budget.*') ? 'active' : '' }}">
                <i class="bi bi-pie-chart"></i>
                <span>{{ __('erp.nav.budget_cost') }}</span>
            </a>
            @endif

            <div class="nav-category">সংগ্রহ ও ইনভেন্টরি (Procurement & Stock)</div>
            @can('mr.view')
            <a href="{{ route('material-requisitions.index') }}" class="nav-item-link {{ request()->routeIs('material-requisitions.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>উপকরণ চাহিদা (MR)</span>
            </a>
            @endcan
            @can('po.view')
            <a href="{{ route('purchase-orders.index') }}" class="nav-item-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart3"></i>
                <span>ক্রয়াদেশ (Purchase Orders)</span>
            </a>
            @endcan
            @can('grn.view')
            <a href="{{ route('goods-receipts.index') }}" class="nav-item-link {{ request()->routeIs('goods-receipts.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>পণ্য প্রাপ্তি (GRN)</span>
            </a>
            @endcan
            @can('stock.view')
            <a href="{{ route('inventory.ledger') }}" class="nav-item-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i>
                <span>স্টক খতিয়ান (Inventory Ledger)</span>
            </a>
            @endcan
            @can('warehouse.view')
            <a href="{{ route('warehouses.index') }}" class="nav-item-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i>
                <span>গোডাউন ও সাইট স্টোর</span>
            </a>
            @endcan

            <div class="nav-category">উপাদান ও মাস্টার ডাটা (Materials & Masters)</div>
            @can('item.view')
            <a href="{{ route('items.index') }}" class="nav-item-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>{{ __('erp.nav.items') }}</span>
            </a>
            <a href="{{ route('item-categories.index') }}" class="nav-item-link {{ request()->routeIs('item-categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span>আইটেম ক্যাটাগরি (Categories)</span>
            </a>
            <a href="{{ route('units.index') }}" class="nav-item-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers"></i>
                <span>পরিমাপের একক (Units)</span>
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
                <span>কস্ট কোড ও ডব্লিউবিএস</span>
            </a>
            @endcan

            <div class="nav-category">{{ __('erp.nav.organizations') }}</div>
            @can('company.view')
            <a href="{{ route('companies.index') }}" class="nav-item-link {{ request()->routeIs('companies.*') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i>
                <span>{{ __('erp.nav.companies') }}</span>
            </a>
            @endcan
            @can('branch.view')
            <a href="{{ route('branches.index') }}" class="nav-item-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i>
                <span>শাখা ব্যবস্থাপনা (Branches)</span>
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

                <!-- Company Selector -->
                @if(isset($userCompanies) && $userCompanies->count() > 1)
                <form action="{{ route('company.switch') }}" method="POST" class="d-none d-md-flex align-items-center">
                    @csrf
                    <i class="bi bi-building text-primary me-2"></i>
                    <select name="company_id" class="form-select form-select-sm tenant-select" onchange="this.form.submit()">
                        @foreach($userCompanies as $comp)
                            <option value="{{ $comp->id }}" {{ (isset($currentCompany) && $currentCompany->id == $comp->id) ? 'selected' : '' }}>
                                {{ $comp->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @elseif(isset($currentCompany))
                <div class="d-none d-md-flex align-items-center text-muted small">
                    <i class="bi bi-building text-primary me-2"></i>
                    <strong class="text-dark">{{ $currentCompany->name }}</strong>
                </div>
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
                <!-- Language Switcher -->
                <div class="btn-group">
                    <a href="{{ route('locale.switch', app()->getLocale() == 'bn' ? 'en' : 'bn') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" title="Switch Language">
                        <i class="bi bi-translate"></i>
                        <span>{{ __('erp.switch_language') }}</span>
                    </a>
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

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebarMenu')?.classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
