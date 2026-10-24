@extends('layouts.app')

@section('title', 'Daily Progress Reports (DPR)')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Daily Progress Reports (DPR)</h4>
        <p class="text-muted small mb-0">Site execution logs, workforce attendance, weather conditions, and work completed</p>
    </div>
    @can('dpr.create')
    <a href="{{ route('dpr.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>New Daily Report</span>
    </a>
    @endcan
</div>

<!-- Filters & Approval Workflow Tabs -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- DPR Approval Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ in_array($status, ['unapproved', 'submitted']) ? 'active' : '' }}" 
                       href="{{ route('dpr.index', ['status' => 'submitted']) }}">
                        <i class="bi bi-clock-history me-1"></i>
                        <span>Pending Approval (Submitted)</span>
                        <span class="badge {{ in_array($status, ['unapproved', 'submitted']) ? 'bg-white text-primary' : 'bg-warning-subtle text-warning-emphasis' }} ms-1 rounded-pill">
                            {{ $counts['unapproved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'approved' ? 'active' : '' }}" 
                       href="{{ route('dpr.index', ['status' => 'approved']) }}">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span>Approved</span>
                        <span class="badge {{ $status === 'approved' ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('dpr.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Reports</span>
                        <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Specific Filter Dropdown -->
            <form method="GET" action="{{ route('dpr.index') }}" class="d-flex align-items-center gap-2">
                <label class="form-label small text-muted mb-0 text-nowrap">Filter By:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="submitted" {{ in_array($status, ['submitted', 'unapproved']) ? 'selected' : '' }}>Pending Approval (Submitted)</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                </select>
                @if(! in_array($status, ['unapproved', 'submitted']))
                <a href="{{ route('dpr.index') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset to Pending Approval">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- DPR Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>DPR Number</th>
                        <th>Project</th>
                        <th>Report Date</th>
                        <th>Weather</th>
                        <th>Workforce</th>
                        <th>Site Engineer</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                    <tr>
                        <td>
                            <a href="{{ route('dpr.show', $report) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                {{ $report->dpr_number }}
                            </a>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $report->project?->name }}</strong>
                            <small class="text-muted">{{ $report->project?->code }}</small>
                        </td>
                        <td class="text-secondary fw-semibold">
                            {{ $report->report_date->format('d-m-Y') }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-cloud-sun me-1"></i> {{ $report->weather }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border">
                                {{ $report->total_workers }} Workers
                            </span>
                            <small class="text-muted d-block">({{ $report->skilled_workers }} Skilled / {{ $report->unskilled_workers }} Unskilled)</small>
                        </td>
                        <td>
                            <span class="text-dark small fw-medium">{{ $report->siteEngineer?->name ?? '—' }}</span>
                        </td>
                        <td>
                            @if($report->status === 'approved')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle me-1"></i> Approved
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                <i class="bi bi-clock me-1"></i> Submitted
                            </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('dpr.show', $report) }}" class="btn btn-sm btn-outline-primary text-nowrap" title="View details">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                                @can('dpr.approve')
                                @if($report->status !== 'approved')
                                <form action="{{ route('dpr.approve', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve Daily Progress Report {{ $report->dpr_number }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success text-nowrap shadow-sm" title="Approve DPR">
                                        <i class="bi bi-check2-circle me-1"></i> Approve
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            @if(in_array($status, ['unapproved', 'submitted']))
                                <span>No daily progress reports currently pending approval.</span>
                                <div class="mt-2">
                                    <a href="{{ route('dpr.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Reports ({{ $counts['all'] }})
                                    </a>
                                </div>
                            @elseif($status === 'approved')
                                <span>No approved daily progress reports found.</span>
                            @else
                                <span>No Daily Progress Reports recorded.</span>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection
