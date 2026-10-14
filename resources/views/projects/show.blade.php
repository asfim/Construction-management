@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
            <span class="badge badge-status badge-{{ $project->status }}">
                {{ __('erp.status.' . $project->status) }}
            </span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">{{ $project->name }}</h4>
        <p class="text-muted small mb-0">
            <i class="bi bi-geo-alt me-1"></i> {{ $project->site_address ?? 'No site address specified' }}
            @if($project->city) ({{ $project->city }}, {{ $project->division }}) @endif
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('projects.boqs.index', $project) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>BOQ & Estimates</span>
        </a>
        <a href="{{ route('projects.budget.show', $project) }}" class="btn btn-warning d-flex align-items-center gap-2 shadow-sm text-dark fw-semibold">
            <i class="bi bi-pie-chart"></i>
            <span>Budget & Costs</span>
        </a>
        @can('project.edit')
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
            <i class="bi bi-pencil-square"></i>
            <span>{{ __('erp.actions.edit') }}</span>
        </a>
        @endcan
        <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('erp.actions.back') }}</span>
        </a>
    </div>
</div>

<!-- Financial & Progress Summary Cards -->
<div class="row g-4 mb-4">
    <!-- Contract Value -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted fw-semibold small mb-2 d-block">{{ __('erp.project.contract_value') }}</span>
            <h4 class="fw-bold text-dark mb-1">৳ {{ number_format($project->contract_value, 2) }}</h4>
            <small class="text-success"><i class="bi bi-check-circle me-1"></i> Total Contract Value</small>
        </div>
    </div>

    <!-- Approved Budget -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted fw-semibold small mb-2 d-block">{{ __('erp.project.budget_amount') }}</span>
            <h4 class="fw-bold text-dark mb-1">৳ {{ number_format($project->budget_amount, 2) }}</h4>
            <small class="text-muted">Internal Approved Budget</small>
        </div>
    </div>

    <!-- Estimated Margin / Buffer -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted fw-semibold small mb-2 d-block">Budget Margin (Markup)</span>
            @php
                $margin = $project->contract_value - $project->budget_amount;
                $marginPercent = $project->contract_value > 0 ? ($margin / $project->contract_value) * 100 : 0;
            @endphp
            <h4 class="fw-bold {{ $margin >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                ৳ {{ number_format($margin, 2) }}
            </h4>
            <small class="text-muted">Margin: {{ number_format($marginPercent, 1) }}%</small>
        </div>
    </div>

    <!-- Physical Progress -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted fw-semibold small mb-2 d-block">{{ __('erp.project.progress') }}</span>
            <h4 class="fw-bold text-primary mb-1">{{ number_format($project->progress_percent, 1) }}%</h4>
            <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Client & Contract Details -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-briefcase text-primary me-2"></i> Contract & Client Information
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 40%;">{{ __('erp.project.client') }}:</td>
                            <td class="fw-semibold text-dark">{{ $project->client_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.client_contact') }}:</td>
                            <td class="text-dark">{{ $project->client_contact_person ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.client_phone') }}:</td>
                            <td class="text-dark">{{ $project->client_phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.client_email') }}:</td>
                            <td class="text-dark">{{ $project->client_email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Branch:</td>
                            <td class="text-dark">{{ $project->branch?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.manager') }}:</td>
                            <td class="fw-semibold text-primary">{{ $project->projectManager?->name ?? 'Unassigned' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.retention') }}:</td>
                            <td class="text-dark">{{ number_format($project->retention_percentage, 1) }}%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.advance_recovery') }}:</td>
                            <td class="text-dark">{{ number_format($project->advance_recovery_percentage, 1) }}%</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.start_date') }}:</td>
                            <td class="text-dark">{{ $project->start_date ? $project->start_date->format('d-m-Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">{{ __('erp.project.end_date') }}:</td>
                            <td class="text-dark">{{ $project->end_date ? $project->end_date->format('d-m-Y') : '—' }}</td>
                        </tr>
                    </tbody>
                </table>

                @if($project->description)
                <div class="mt-4 pt-3 border-top">
                    <small class="text-muted fw-bold d-block mb-1">{{ __('erp.project.description') }}:</small>
                    <p class="small text-secondary mb-0">{{ $project->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Site Team Members -->
    <div class="col-12 col-lg-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-success me-2"></i> {{ __('erp.project.team') }}
                </h6>
                <span class="badge bg-light text-dark border">{{ $project->members->count() }} Members</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>Name & Designation</th>
                                <th>Site Role</th>
                                <th>Contact</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->members as $member)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $member->name }}</div>
                                    <small class="text-muted">{{ $member->designation ?? 'Engineer' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border font-monospace">
                                        {{ str_replace('_', ' ', strtoupper($member->pivot->project_role ?? 'SITE')) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i> {{ $member->phone ?? '—' }}</small>
                                    <small class="text-muted d-block"><i class="bi bi-envelope me-1"></i> {{ $member->email }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No site team members assigned yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
