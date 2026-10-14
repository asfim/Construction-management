@extends('layouts.app')

@section('title', __('erp.dashboard.title'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.dashboard.title') }}</h4>
        <p class="text-muted small mb-0">
            {{ __('erp.active_company') }}: <strong class="text-primary">{{ $currentCompany?->name ?? 'N/A' }}</strong>
            @if($currentProject)
            | {{ __('erp.active_project') }}: <strong class="text-success">{{ $currentProject->name }}</strong>
            @endif
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('project.create')
        <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>{{ __('erp.dashboard.create_project') }}</span>
        </a>
        @endcan
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-kanban"></i>
            <span>{{ __('erp.nav.project_list') }}</span>
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="row g-4 mb-4">
    <!-- Active Projects -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">{{ __('erp.dashboard.active_projects') }}</span>
                <div class="rounded-3 p-2 bg-primary-subtle text-primary">
                    <i class="bi bi-buildings fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $activeProjectsCount }}</h3>
            <small class="text-success d-flex align-items-center gap-1">
                <i class="bi bi-arrow-up-right"></i> {{ __('erp.status.active') }}
            </small>
        </div>
    </div>

    <!-- Total Contract Value -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">{{ __('erp.dashboard.total_contract_value') }}</span>
                <div class="rounded-3 p-2 bg-success-subtle text-success">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">৳ {{ number_format($totalContractValue, 2) }}</h4>
            <small class="text-muted">{{ __('erp.currency') }}</small>
        </div>
    </div>

    <!-- Approved Budget -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">{{ __('erp.dashboard.approved_budget') }}</span>
                <div class="rounded-3 p-2 bg-warning-subtle text-warning">
                    <i class="bi bi-pie-chart-fill fs-5"></i>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">৳ {{ number_format($totalBudgetAmount, 2) }}</h4>
            <small class="text-muted">{{ __('erp.dashboard.approved_budget') }}</small>
        </div>
    </div>

    <!-- Assigned Engineers & Members -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 h-100 border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted fw-semibold small">{{ __('erp.dashboard.team_members') }}</span>
                <div class="rounded-3 p-2 bg-info-subtle text-info">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $teamCount }}</h3>
            <small class="text-muted">{{ __('erp.project.team') }}</small>
        </div>
    </div>
</div>

<!-- Main Row: Projects Table & Side Widgets -->
<div class="row g-4">
    <!-- Projects Overview Table -->
    <div class="col-12 col-lg-8">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-kanban me-2 text-primary"></i> {{ __('erp.dashboard.recent_projects') }}
                </h6>
                <a href="{{ route('projects.index') }}" class="btn btn-sm btn-link text-decoration-none">
                    {{ __('erp.actions.view') }} <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentProjects->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                    {{ __('erp.dashboard.no_projects_found') }}
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>{{ __('erp.project.code') }}</th>
                                <th>{{ __('erp.project.name') }}</th>
                                <th>{{ __('erp.project.contract_value') }}</th>
                                <th>{{ __('erp.project.progress') }}</th>
                                <th>{{ __('erp.actions.details') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProjects as $project)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('projects.show', $project) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $project->name }}
                                    </a>
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $project->city ?? 'Bangladesh' }}
                                    </small>
                                </td>
                                <td class="fw-semibold text-dark">
                                    ৳ {{ number_format($project->contract_value, 2) }}
                                </td>
                                <td style="min-width: 130px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                                        </div>
                                        <small class="text-muted fw-semibold">{{ number_format($project->progress_percent, 1) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-status badge-{{ $project->status }}">
                                        {{ __('erp.status.' . $project->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Side Column: Stage Breakdown & Recent Activity -->
    <div class="col-12 col-lg-4 d-flex flex-column gap-4">
        <!-- Project Stages Card -->
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart text-success me-2"></i> {{ __('erp.dashboard.progress_overview') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-muted">{{ __('erp.status.active') }}</span>
                    <span class="badge bg-success-subtle text-success">{{ $statusBreakdown['active'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-muted">{{ __('erp.status.planning') }}</span>
                    <span class="badge bg-primary-subtle text-primary">{{ $statusBreakdown['planning'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-semibold text-muted">{{ __('erp.status.on_hold') }}</span>
                    <span class="badge bg-warning-subtle text-warning">{{ $statusBreakdown['on_hold'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small fw-semibold text-muted">{{ __('erp.status.completed') }}</span>
                    <span class="badge bg-secondary-subtle text-secondary">{{ $statusBreakdown['completed'] }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Audit Trail Card -->
        <div class="card card-custom border-0 shadow-sm flex-grow-1">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history text-info me-2"></i> {{ __('erp.dashboard.recent_activity') }}
                </h6>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-link text-decoration-none">
                    {{ __('erp.actions.view') }}
                </a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    @forelse($auditLogs as $log)
                    <li class="list-group-item px-3 py-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-dark">{{ $log->user_name ?? 'System' }}</strong>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.7rem;">
                                {{ $log->action }}
                            </span>
                        </div>
                        <div class="text-muted text-truncate" style="max-width: 250px;">
                            {{ $log->description }}
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">
                            {{ $log->created_at->diffForHumans() }}
                        </small>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-4 text-muted">
                        কোনো অডিট রেকর্ড পাওয়া যায়নি।
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
