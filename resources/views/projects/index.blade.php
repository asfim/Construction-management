@extends('layouts.app')

@section('title', __('erp.nav.project_list'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.project_list') }}</h4>
        <p class="text-muted small mb-0">All construction projects and active site supervision list</p>
    </div>
    @can('project.create')
    <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>{{ __('erp.dashboard.create_project') }}</span>
    </a>
    @endcan
</div>

<!-- Search & Filters -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('erp.actions.search') }}" value="{{ $search }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="planning" {{ $status == 'planning' ? 'selected' : '' }}>{{ __('erp.status.planning') }}</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ __('erp.status.active') }}</option>
                    <option value="on_hold" {{ $status == 'on_hold' ? 'selected' : '' }}>{{ __('erp.status.on_hold') }}</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>{{ __('erp.status.completed') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> {{ __('erp.actions.filter') }}
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Projects Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>{{ __('erp.project.code') }}</th>
                        <th>{{ __('erp.project.name') }}</th>
                        <th>{{ __('erp.project.client') }}</th>
                        <th>{{ __('erp.project.contract_value') }}</th>
                        <th>{{ __('erp.project.budget_amount') }}</th>
                        <th>{{ __('erp.project.progress') }}</th>
                        <th>Status</th>
                        <th class="text-end">{{ __('erp.actions.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
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
                                @if($project->branch)
                                | <i class="bi bi-building me-1"></i> {{ $project->branch->name }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <span class="text-dark small fw-medium">{{ $project->client_name ?? '—' }}</span>
                        </td>
                        <td class="fw-semibold text-dark">
                            ৳ {{ number_format($project->contract_value, 2) }}
                        </td>
                        <td class="fw-semibold text-muted">
                            ৳ {{ number_format($project->budget_amount, 2) }}
                        </td>
                        <td style="min-width: 120px;">
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
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projects.show', $project) }}">
                                            <i class="bi bi-eye text-primary me-2"></i> {{ __('erp.actions.view') }}
                                        </a>
                                    </li>
                                    @can('project.edit')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                            <i class="bi bi-pencil-square text-warning me-2"></i> {{ __('erp.actions.edit') }}
                                        </a>
                                    </li>
                                    @endcan
                                    @can('project.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('{{ __('erp.actions.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash text-danger me-2"></i> {{ __('erp.actions.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                            {{ __('erp.dashboard.no_projects_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection
