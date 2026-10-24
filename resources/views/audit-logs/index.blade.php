@extends('layouts.app')

@section('title', __('erp.nav.audit_logs'))

@section('content')
<!-- Print Document Official Header -->
<div class="d-none d-print-block mb-3">
    <x-print-header 
        title="SYSTEM AUDIT TRAIL & COMPLIANCE LOG"
        subtitle="Official immutable event log & activity history"
        docNumberLabel="Total Logs"
        :docNumber="$logs->total() . ' Records'"
        docDateLabel="Print Date"
        :docDate="now()->format('d M Y, h:i A')"
        status="Official Record"
        statusClass="bg-dark"
        :extraMeta="array_filter([
            'Action Filter' => !empty($action) ? ucfirst($action) : 'All Actions',
            'Module Filter' => !empty($entityType) ? ($availableEntities[$entityType] ?? $entityType) : null,
            'Printed By' => auth()->user()->name ?? 'System Administrator',
        ])"
    />
</div>

<!-- Page Header (Web Screen Only) -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3 d-print-none">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark">Audit Trail &amp; Security Logs</h4>
            <span class="badge bg-secondary-subtle text-secondary border">
                <i class="bi bi-shield-lock-fill text-primary me-1"></i> Compliance &amp; Security
            </span>
        </div>
        <p class="text-muted small mb-0">Immutable system activity audit trail, change history, and user authentication logs</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-primary d-flex align-items-center gap-2 shadow-sm" title="Download CSV Log">
            <i class="bi bi-download"></i>
            <span>Export CSV</span>
        </a>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm" title="Print Log Report">
            <i class="bi bi-printer"></i>
            <span>Print</span>
        </button>
    </div>
</div>

<!-- KPI / Metric Cards (Web Screen Only) -->
<div class="row g-3 mb-4 d-print-none">
    <!-- Total Events -->
    <div class="col-6 col-md-4 col-xl">
        <a href="{{ route('audit-logs.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ empty($action) ? 'border-primary border-start border-4' : '' }} hover-shadow transition-all bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Logs</span>
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-activity"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">All logged events</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Creations -->
    <div class="col-6 col-md-4 col-xl">
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['action' => 'created'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $action === 'created' ? 'border-success border-start border-4' : '' }} hover-shadow transition-all bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Created</span>
                        <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($stats['created']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">New records</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Updates -->
    <div class="col-6 col-md-4 col-xl">
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['action' => 'updated'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $action === 'updated' ? 'border-warning border-start border-4' : '' }} hover-shadow transition-all bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Updated</span>
                        <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-warning-emphasis">{{ number_format($stats['updated']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Field modifications</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Deletions -->
    <div class="col-6 col-md-6 col-xl">
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['action' => 'deleted'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $action === 'deleted' ? 'border-danger border-start border-4' : '' }} hover-shadow transition-all bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Deleted</span>
                        <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-trash3"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['deleted']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">Purged items</small>
                </div>
            </div>
        </a>
    </div>

    <!-- Auth Sessions -->
    <div class="col-12 col-md-6 col-xl">
        <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['action' => 'auth'])) }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3 h-100 {{ $action === 'auth' ? 'border-info border-start border-4' : '' }} hover-shadow transition-all bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Logins &amp; Auth</span>
                        <div class="rounded-circle bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-check"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-info-emphasis">{{ number_format($stats['auth']) }}</h3>
                    <small class="text-muted" style="font-size: 0.75rem;">User sessions</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Advanced Search & Filter Bar (Web Screen Only) -->
<div class="card card-custom border-0 shadow-sm mb-4 d-print-none">
    <div class="card-body p-3 p-md-4">
        <form method="GET" action="{{ route('audit-logs.index') }}">
            <div class="row g-3">
                <!-- Search Term -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">Keyword Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="User, IP, ID, or description..." value="{{ $search }}">
                    </div>
                </div>

                <!-- Action Filter -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Action Type</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        <option value="created" {{ $action == 'created' ? 'selected' : '' }}>Created (+)</option>
                        <option value="updated" {{ $action == 'updated' ? 'selected' : '' }}>Updated (✎)</option>
                        <option value="deleted" {{ $action == 'deleted' ? 'selected' : '' }}>Deleted (✕)</option>
                        <option value="auth" {{ $action == 'auth' ? 'selected' : '' }}>Login / Logout</option>
                    </select>
                </div>

                <!-- Module / Entity Filter -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Module / Entity</label>
                    <select name="entity_type" class="form-select">
                        <option value="">All Modules</option>
                        @foreach($availableEntities as $key => $label)
                        <option value="{{ $key }}" {{ $entityType == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- User Filter -->
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold text-muted mb-1">User Account</label>
                    <select name="user_id" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->designation ?? 'User' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter & Reset Buttons -->
                <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>
                    @if(!empty($action) || !empty($userId) || !empty($entityType) || !empty($search) || !empty($dateFrom) || !empty($dateTo))
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-danger" title="Clear All Filters">
                        <i class="bi bi-x-lg"></i>
                    </a>
                    @else
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </a>
                    @endif
                </div>

                <!-- Date Range Accordion / Row -->
                <div class="col-12 pt-2 border-top">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="small text-muted fw-semibold"><i class="bi bi-calendar3 me-1"></i> Date Filter:</span>
                        </div>
                        <div class="col-12 col-sm-4 col-md-3">
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" placeholder="From Date">
                        </div>
                        <div class="col-auto text-muted small">to</div>
                        <div class="col-12 col-sm-4 col-md-3">
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" placeholder="To Date">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Active Filter Badges (Web Screen Only) -->
@if(!empty($action) || !empty($userId) || !empty($entityType) || !empty($search) || !empty($dateFrom) || !empty($dateTo))
<div class="d-flex align-items-center flex-wrap gap-2 mb-3 d-print-none">
    <span class="small text-muted fw-semibold">Active Filters:</span>
    @if(!empty($search))
        <span class="badge bg-light text-dark border">
            Search: "{{ $search }}"
            <a href="{{ route('audit-logs.index', request()->except('search')) }}" class="text-muted ms-1"><i class="bi bi-x"></i></a>
        </span>
    @endif
    @if(!empty($action))
        <span class="badge bg-light text-dark border">
            Action: {{ ucfirst($action) }}
            <a href="{{ route('audit-logs.index', request()->except('action')) }}" class="text-muted ms-1"><i class="bi bi-x"></i></a>
        </span>
    @endif
    @if(!empty($entityType))
        <span class="badge bg-light text-dark border">
            Module: {{ $availableEntities[$entityType] ?? $entityType }}
            <a href="{{ route('audit-logs.index', request()->except('entity_type')) }}" class="text-muted ms-1"><i class="bi bi-x"></i></a>
        </span>
    @endif
    @if(!empty($userId))
        @php $filteredUser = $users->firstWhere('id', $userId); @endphp
        <span class="badge bg-light text-dark border">
            User: {{ $filteredUser->name ?? $userId }}
            <a href="{{ route('audit-logs.index', request()->except('user_id')) }}" class="text-muted ms-1"><i class="bi bi-x"></i></a>
        </span>
    @endif
    @if(!empty($dateFrom) || !empty($dateTo))
        <span class="badge bg-light text-dark border">
            Date: {{ $dateFrom ?? 'Start' }} → {{ $dateTo ?? 'Now' }}
            <a href="{{ route('audit-logs.index', request()->except(['date_from', 'date_to'])) }}" class="text-muted ms-1"><i class="bi bi-x"></i></a>
        </span>
    @endif
    <a href="{{ route('audit-logs.index') }}" class="btn btn-link btn-sm text-danger text-decoration-none p-0 ms-2 small">Reset All</a>
</div>
@endif

<!-- Audit Trail Table -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted small text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="col-timestamp ps-3">Timestamp</th>
                        <th class="col-user">User &amp; Origin</th>
                        <th class="col-action">Action</th>
                        <th class="col-entity">Entity &amp; Target</th>
                        <th class="col-summary">Activity Summary</th>
                        <th class="col-ip">IP / Network</th>
                        <th class="text-end pe-3 d-print-none col-details" style="width: 100px;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $badgeClass = match($log->action) {
                            'created' => 'bg-success-subtle text-success border border-success-subtle',
                            'updated' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                            'deleted' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'login' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                            'logout' => 'bg-secondary-subtle text-secondary border',
                            default => 'bg-light text-dark border',
                        };

                        $actionIcon = match($log->action) {
                            'created' => 'bi-plus-circle',
                            'updated' => 'bi-pencil-square',
                            'deleted' => 'bi-trash3',
                            'login' => 'bi-box-arrow-in-right',
                            'logout' => 'bi-box-arrow-right',
                            default => 'bi-record-circle',
                        };

                        $entityName = class_basename($log->auditable_type);
                        $entityIcon = match($entityName) {
                            'Project' => 'bi-kanban',
                            'MaterialRequisition' => 'bi-file-earmark-text',
                            'PurchaseOrder' => 'bi-cart-check',
                            'GoodsReceipt' => 'bi-box-seam',
                            'StockIssue' => 'bi-boxes',
                            'ClientBill' => 'bi-receipt-cutoff',
                            'DailyProgressReport' => 'bi-clipboard-data',
                            'ProjectExpense' => 'bi-cash-coin',
                            'BoqItem', 'BoqVersion' => 'bi-table',
                            'Budget' => 'bi-calculator',
                            'Item' => 'bi-bricks',
                            'Supplier' => 'bi-truck',
                            'Company' => 'bi-buildings',
                            'User' => 'bi-person',
                            'Warehouse' => 'bi-shop',
                            'Branch' => 'bi-diagram-3',
                            default => 'bi-file-earmark',
                        };

                        $hasDiff = !empty($log->old_values) && !empty($log->new_values);
                        $hasNew = !empty($log->new_values);
                        $hasOld = !empty($log->old_values);

                        $changedKeys = [];
                        if ($hasDiff) {
                            $changedKeys = array_keys(array_diff_assoc($log->new_values, $log->old_values));
                        } elseif ($hasNew) {
                            $changedKeys = array_keys($log->new_values);
                        }
                    @endphp
                    <tr>
                        <!-- Timestamp -->
                        <td class="col-timestamp ps-3">
                            <div class="fw-semibold text-dark small">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock me-1 d-print-none"></i>{{ $log->created_at->format('h:i:s A') }}
                                <span class="badge bg-light text-secondary border ms-1 d-print-none" style="font-size: 0.68rem;">
                                    {{ $log->created_at->diffForHumans(null, true) }}
                                </span>
                            </div>
                        </td>

                        <!-- User & Origin -->
                        <td class="col-user">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center d-print-none" style="width: 30px; height: 30px; font-size: 0.8rem; flex-shrink: 0;">
                                    {{ mb_substr($log->user_name ?? 'S', 0, 1) }}
                                </div>
                                <div class="text-break">
                                    <span class="fw-semibold text-dark d-block leading-tight small">
                                        {{ $log->user_name ?? 'System' }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        {{ $log->user?->email ?? ($log->user_id ? 'User #' . $log->user_id : 'System Daemon') }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="col-action">
                            <span class="badge {{ $badgeClass }} text-uppercase d-inline-flex align-items-center gap-1 py-1 px-2" style="font-size: 0.72rem;">
                                <i class="bi {{ $actionIcon }} d-print-none"></i>
                                <span>{{ $log->action }}</span>
                            </span>
                        </td>

                        <!-- Entity & Target -->
                        <td class="col-entity">
                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                    <i class="bi {{ $entityIcon }} text-primary d-print-none"></i>
                                    <strong>{{ $entityName ?: 'System' }}</strong>
                                    @if($log->auditable_id)
                                    <span class="text-muted">#{{ $log->auditable_id }}</span>
                                    @endif
                                </span>
                                @if($log->project)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Associated Project">
                                    <i class="bi bi-kanban me-1 d-print-none"></i>{{ Str::limit($log->project->name, 22) }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <!-- Description & Inline Diff Summary -->
                        <td class="col-summary">
                            <div class="small text-dark font-sans-serif">{{ $log->description }}</div>
                            @if(!empty($changedKeys))
                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.68rem;">
                                    {{ count($changedKeys) }} {{ count($changedKeys) === 1 ? 'field' : 'fields' }}:
                                </span>
                                @foreach(array_slice($changedKeys, 0, 4) as $k)
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.68rem;">{{ $k }}</span>
                                @endforeach
                                @if(count($changedKeys) > 4)
                                <span class="text-muted small" style="font-size: 0.68rem;">+{{ count($changedKeys) - 4 }} more</span>
                                @endif
                            </div>
                            @endif
                        </td>

                        <!-- IP Address / Network -->
                        <td class="col-ip">
                            <div class="small font-monospace text-dark">
                                <i class="bi bi-globe me-1 text-muted d-print-none"></i>{{ $log->ip_address ?? '—' }}
                            </div>
                            @if($log->user_agent)
                            <div class="text-muted text-truncate d-print-none" style="font-size: 0.7rem; max-width: 120px;" title="{{ $log->user_agent }}">
                                <i class="bi bi-laptop me-1"></i>{{ Str::limit($log->user_agent, 18) }}
                            </div>
                            @endif
                        </td>

                        <!-- Details Action (Web Screen Only) -->
                        <td class="text-end pe-3 d-print-none col-details">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 shadow-sm inspect-log-btn"
                                    data-id="{{ $log->id }}"
                                    data-action="{{ $log->action }}"
                                    data-action-icon="{{ $actionIcon }}"
                                    data-badge-class="{{ $badgeClass }}"
                                    data-entity="{{ $entityName }}"
                                    data-entity-id="{{ $log->auditable_id }}"
                                    data-user="{{ $log->user_name ?? 'System' }}"
                                    data-timestamp="{{ $log->created_at->format('d-m-Y H:i:s') }}"
                                    data-ip="{{ $log->ip_address ?? 'N/A' }}"
                                    data-agent="{{ $log->user_agent ?? '' }}"
                                    data-project="{{ $log->project ? $log->project->name . ' (' . $log->project->code . ')' : '' }}"
                                    data-description="{{ $log->description }}"
                                    data-old="{{ json_encode($log->old_values) }}"
                                    data-new="{{ json_encode($log->new_values) }}">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-xl-inline">Inspect</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="rounded-circle bg-light d-inline-flex p-3 text-muted mb-3">
                                    <i class="bi bi-search fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Audit Records Found</h6>
                                <p class="text-muted small mb-3">No system activity logs matched your selected filter criteria.</p>
                                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-outline-primary d-print-none">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All Filters
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Footer (Web Screen Only) -->
    @if($logs->hasPages())
    <div class="card-footer bg-white border-top py-3 d-print-none">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<!-- Print Document Official Footer -->
<div class="d-none d-print-block mt-4">
    <x-print-footer 
        preparedTitle="Audited / Printed By"
        :preparedBy="auth()->user()->name ?? 'System Administrator'"
        :preparedDate="now()->format('d M Y')"
        checkedTitle="Verified By (Compliance)"
        checkedBy="Internal Audit & QA"
        :checkedDate="now()->format('d M Y')"
        approvedTitle="Approved By (Management)"
        approvedBy="Managing Director"
        :approvedDate="now()->format('d M Y')"
        notes="This audit trail report is generated directly from immutable system audit records. Any unauthorized alteration or reproduction is strictly prohibited."
    />
</div>

<!-- SINGLE REUSABLE AUDIT LOG DETAILS MODAL (Web Screen Only, Placed outside table) -->
<div class="modal fade d-print-none" id="auditInspectModal" tabindex="-1" aria-labelledby="auditInspectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-3">
                <div class="d-flex align-items-center gap-2">
                    <span id="modalActionBadge" class="badge text-uppercase px-2 py-1"></span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="auditInspectModalLabel"></h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Event Summary Grid -->
                <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block fw-semibold">Timestamp</small>
                        <span id="modalTimestamp" class="fw-bold text-dark small"></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block fw-semibold">User</small>
                        <span id="modalUser" class="fw-bold text-dark small"></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block fw-semibold">Entity Type</small>
                        <span id="modalEntity" class="fw-bold text-dark small font-monospace"></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block fw-semibold">IP Address</small>
                        <span id="modalIp" class="fw-bold text-dark small font-monospace"></span>
                    </div>
                    <div class="col-12 col-md-6" id="modalProjectContainer">
                        <small class="text-muted d-block fw-semibold">Associated Project</small>
                        <span id="modalProject" class="fw-semibold text-primary small"></span>
                    </div>
                    <div class="col-12 col-md-6" id="modalDescContainer">
                        <small class="text-muted d-block fw-semibold">Full Description</small>
                        <span id="modalDescription" class="text-dark small"></span>
                    </div>
                    <div class="col-12" id="modalAgentContainer">
                        <small class="text-muted d-block fw-semibold">Client User Agent</small>
                        <span id="modalAgent" class="text-muted small font-monospace" style="font-size: 0.75rem;"></span>
                    </div>
                </div>

                <!-- Container for Dynamic Diff / Values -->
                <div id="modalChangesContainer"></div>

                <!-- Raw JSON Accordion -->
                <details class="mt-3" id="modalRawJsonContainer">
                    <summary class="text-primary small fw-semibold" style="cursor: pointer;">
                        <i class="bi bi-code-slash me-1"></i> Show Raw JSON Payload (Developer View)
                    </summary>
                    <div class="mt-2" id="modalRawJsonContent"></div>
                </details>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.transition-all {
    transition: all 0.2s ease-in-out;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
}

@page {
    size: landscape;
    margin: 8mm 6mm;
}

@media print {
    /* Hide all web chrome elements */
    .d-print-none,
    .topbar,
    .sidebar,
    .btn,
    .card-footer,
    form,
    .badge-filter,
    .footer {
        display: none !important;
    }

    body {
        background: #ffffff !important;
        color: #0f172a !important;
        font-size: 8pt !important;
    }

    .main-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        min-height: auto !important;
    }

    .content-body {
        padding: 0 !important;
        margin: 0 !important;
    }

    .card, .card-custom {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .table-responsive {
        overflow: visible !important;
        display: block !important;
        width: 100% !important;
    }

    /* Fixed table layout guaranteeing 100% fit on page */
    .table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
        border-collapse: collapse !important;
        font-size: 7.8pt !important;
    }

    .col-timestamp { width: 14% !important; }
    .col-user      { width: 16% !important; }
    .col-action    { width: 10% !important; }
    .col-entity    { width: 18% !important; }
    .col-summary   { width: 30% !important; }
    .col-ip        { width: 12% !important; }

    .table th, .table td {
        padding: 4px 5px !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        white-space: normal !important;
        vertical-align: top !important;
        border: 1px solid #cbd5e1 !important;
    }

    .table > thead > tr > th {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 7.5pt !important;
        text-transform: uppercase !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .badge {
        border: 1px solid #94a3b8 !important;
        background: #f8fafc !important;
        color: #0f172a !important;
        font-size: 7pt !important;
        padding: 1px 3px !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    tr {
        page-break-inside: avoid !important;
    }

    .print-document-header {
        page-break-after: avoid !important;
    }
    .print-document-footer {
        page-break-inside: avoid !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('auditInspectModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.inspect-log-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const action = this.dataset.action;
            const actionIcon = this.dataset.actionIcon;
            const badgeClass = this.dataset.badgeClass;
            const entity = this.dataset.entity;
            const entityId = this.dataset.entityId;
            const user = this.dataset.user;
            const timestamp = this.dataset.timestamp;
            const ip = this.dataset.ip;
            const agent = this.dataset.agent;
            const project = this.dataset.project;
            const description = this.dataset.description;

            let oldValues = null;
            let newValues = null;
            try { oldValues = JSON.parse(this.dataset.old || 'null'); } catch(e) {}
            try { newValues = JSON.parse(this.dataset.new || 'null'); } catch(e) {}

            // Populate header & badge
            const badgeEl = document.getElementById('modalActionBadge');
            badgeEl.className = 'badge text-uppercase px-2 py-1 ' + badgeClass;
            badgeEl.innerHTML = `<i class="bi ${actionIcon} me-1"></i> ${action}`;

            document.getElementById('auditInspectModalLabel').textContent = `Audit Event #${id}: ${entity} ${entityId ? '#' + entityId : ''}`;
            document.getElementById('modalTimestamp').textContent = timestamp;
            document.getElementById('modalUser').textContent = user;
            document.getElementById('modalEntity').textContent = entity;
            document.getElementById('modalIp').textContent = ip;
            document.getElementById('modalDescription').textContent = description;

            // Project
            const projCont = document.getElementById('modalProjectContainer');
            if (project) {
                projCont.classList.remove('d-none');
                document.getElementById('modalProject').innerHTML = `<i class="bi bi-kanban me-1"></i> ${project}`;
            } else {
                projCont.classList.add('d-none');
            }

            // User Agent
            const agentCont = document.getElementById('modalAgentContainer');
            if (agent) {
                agentCont.classList.remove('d-none');
                document.getElementById('modalAgent').textContent = agent;
            } else {
                agentCont.classList.add('d-none');
            }

            // Changes Container
            const changesCont = document.getElementById('modalChangesContainer');
            changesCont.innerHTML = '';

            const hasOld = oldValues && typeof oldValues === 'object' && Object.keys(oldValues).length > 0;
            const hasNew = newValues && typeof newValues === 'object' && Object.keys(newValues).length > 0;

            if (hasOld && hasNew) {
                // Side by side diff
                const allKeys = Array.from(new Set([...Object.keys(oldValues), ...Object.keys(newValues)]));
                const changedKeys = allKeys.filter(k => JSON.stringify(oldValues[k]) !== JSON.stringify(newValues[k]));

                let rowsHtml = '';
                changedKeys.forEach(field => {
                    const oldVal = oldValues[field];
                    const newVal = newValues[field];

                    const renderVal = (v, isOld) => {
                        if (v === null || v === undefined) return '<span class="badge bg-secondary-subtle text-secondary">null</span>';
                        if (typeof v === 'boolean') return `<span class="badge ${v ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">${v}</span>`;
                        if (typeof v === 'object') return `<pre class="m-0 p-1 bg-white rounded border" style="font-size: 0.72rem;">${escapeHtml(JSON.stringify(v, null, 2))}</pre>`;
                        if (isOld) return `<span class="text-decoration-line-through">${escapeHtml(String(v))}</span>`;
                        return `<span>${escapeHtml(String(v))}</span>`;
                    };

                    rowsHtml += `
                        <tr>
                            <td class="font-monospace fw-semibold text-dark">${escapeHtml(field)}</td>
                            <td class="bg-danger-subtle bg-opacity-25 text-danger font-monospace">${renderVal(oldVal, true)}</td>
                            <td class="bg-success-subtle bg-opacity-25 text-success font-monospace fw-semibold">${renderVal(newVal, false)}</td>
                        </tr>
                    `;
                });

                changesCont.innerHTML = `
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="bi bi-sliders text-warning me-1"></i> Field Level Change Comparison (Diff)
                            </h6>
                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                ${changedKeys.length} Field(s) Modified
                            </span>
                        </div>
                        <div class="table-responsive border rounded">
                            <table class="table table-bordered mb-0 align-middle small">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 28%;">Field / Attribute</th>
                                        <th style="width: 36%;" class="bg-danger-subtle text-danger fw-semibold">
                                            <i class="bi bi-dash-circle me-1"></i> Previous Value (Old)
                                        </th>
                                        <th style="width: 36%;" class="bg-success-subtle text-success fw-semibold">
                                            <i class="bi bi-plus-circle me-1"></i> Updated Value (New)
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>${rowsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else if (hasNew && action === 'created') {
                let rowsHtml = '';
                for (const [k, v] of Object.entries(newValues)) {
                    const formatVal = (val) => {
                        if (val === null || val === undefined) return '<span class="badge bg-secondary-subtle text-secondary">null</span>';
                        if (typeof val === 'boolean') return `<span class="badge ${val ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">${val}</span>`;
                        if (typeof val === 'object') return `<pre class="m-0 p-1 bg-light rounded" style="font-size: 0.72rem;">${escapeHtml(JSON.stringify(val, null, 2))}</pre>`;
                        return escapeHtml(String(val));
                    };
                    rowsHtml += `
                        <tr>
                            <td class="font-monospace text-muted">${escapeHtml(k)}</td>
                            <td class="font-monospace text-dark fw-semibold">${formatVal(v)}</td>
                        </tr>
                    `;
                }

                changesCont.innerHTML = `
                    <div class="mb-4">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="bi bi-check-circle me-1"></i> Initial Record Attributes (Created)
                        </h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-striped table-sm mb-0 small">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 35%;">Attribute</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>${rowsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else if (hasOld && action === 'deleted') {
                let rowsHtml = '';
                for (const [k, v] of Object.entries(oldValues)) {
                    rowsHtml += `
                        <tr>
                            <td class="font-monospace text-muted">${escapeHtml(k)}</td>
                            <td class="font-monospace text-dark">${escapeHtml(String(v))}</td>
                        </tr>
                    `;
                }

                changesCont.innerHTML = `
                    <div class="mb-4">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="bi bi-trash3 me-1"></i> Purged Record Snapshot (Deleted)
                        </h6>
                        <div class="alert alert-danger bg-danger-subtle border-danger-subtle text-danger mb-3 small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> This record was permanently purged from the system. Below is the historical snapshot before deletion.
                        </div>
                        <div class="table-responsive border rounded">
                            <table class="table table-striped table-sm mb-0 small">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th style="width: 35%;">Attribute</th>
                                        <th>Previous Value</th>
                                    </tr>
                                </thead>
                                <tbody>${rowsHtml}</tbody>
                            </table>
                        </div>
                    </div>
                `;
            }

            // Raw JSON
            const rawJsonContent = document.getElementById('modalRawJsonContent');
            rawJsonContent.innerHTML = '';
            if (hasOld) {
                rawJsonContent.innerHTML += `
                    <div class="mb-2">
                        <small class="text-muted fw-bold d-block">old_values:</small>
                        <pre class="bg-dark text-light p-3 rounded mb-0" style="font-size: 0.72rem; max-height: 180px; overflow-y: auto;">${escapeHtml(JSON.stringify(oldValues, null, 2))}</pre>
                    </div>
                `;
            }
            if (hasNew) {
                rawJsonContent.innerHTML += `
                    <div>
                        <small class="text-muted fw-bold d-block">new_values:</small>
                        <pre class="bg-dark text-light p-3 rounded mb-0" style="font-size: 0.72rem; max-height: 180px; overflow-y: auto;">${escapeHtml(JSON.stringify(newValues, null, 2))}</pre>
                    </div>
                `;
            }

            modal.show();
        });
    });

    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
@endsection
