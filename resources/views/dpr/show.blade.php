@extends('layouts.app')

@section('title', "DPR: {$dpr->dpr_number}")

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace fs-6">{{ $dpr->dpr_number }}</span>
            @if($dpr->status === 'approved')
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-check-circle me-1"></i> Approved
            </span>
            @else
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <i class="bi bi-clock me-1"></i> Submitted
            </span>
            @endif
        </div>
        <h4 class="fw-bold mb-1 text-dark">Daily Progress Report: {{ $dpr->report_date?->format('d M, Y') ?? 'N/A' }}</h4>
        <p class="text-muted small mb-0">{{ $dpr->project?->name }} ({{ $dpr->project?->code }})</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('dpr.approve')
        @if($dpr->status !== 'approved')
        <form action="{{ route('dpr.approve', $dpr) }}" method="POST" onsubmit="return confirm('Approve this Daily Progress Report?');">
            @csrf
            <button type="submit" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-check2-circle"></i>
                <span>Approve DPR</span>
            </button>
        </form>
        @endif
        @endcan
        <a href="{{ route('dpr.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>
</div>

<!-- Overview Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Total Workforce</span>
            <h3 class="fw-bold text-dark mb-0">{{ $dpr->total_workers }}</h3>
            <small class="text-muted">{{ $dpr->skilled_workers }} Skilled, {{ $dpr->unskilled_workers }} Unskilled</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Weather Condition</span>
            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-cloud-sun me-1"></i> {{ $dpr->weather }}</h4>
            <small class="text-muted">{{ $dpr->report_date?->format('l, d-m-Y') ?? 'N/A' }}</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Reporting Engineer</span>
            <h5 class="fw-bold text-dark mb-0">{{ $dpr->siteEngineer?->name ?? '—' }}</h5>
            <small class="text-muted">{{ $dpr->siteEngineer?->designation ?? 'Site Engineer' }}</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Approval Status</span>
            @if($dpr->approved_by)
            <h6 class="fw-bold text-success mb-0">Approved by {{ $dpr->approver?->name }}</h6>
            <small class="text-muted">{{ $dpr->approved_at?->format('d-m-Y H:i') }}</small>
            @else
            <h6 class="fw-bold text-warning-emphasis mb-0">Pending PM Review</h6>
            <small class="text-muted">Awaiting certification</small>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Work Executed and Details -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-card-text text-primary me-2"></i> Work Summary & Details
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="text-muted small fw-bold d-block text-uppercase">Work Performed Today:</label>
                    <p class="text-dark mb-0 whitespace-pre-line">{{ $dpr->work_performed }}</p>
                </div>

                @if($dpr->equipment_deployed)
                <div class="mb-3">
                    <label class="text-muted small fw-bold d-block text-uppercase">Equipment Deployed:</label>
                    <p class="text-dark mb-0">{{ $dpr->equipment_deployed }}</p>
                </div>
                @endif

                @if($dpr->material_received_used)
                <div class="mb-3">
                    <label class="text-muted small fw-bold d-block text-uppercase">Material Consumption / Deliveries:</label>
                    <p class="text-dark mb-0">{{ $dpr->material_received_used }}</p>
                </div>
                @endif

                @if($dpr->issues_safety_notes)
                <div class="p-3 bg-light rounded-3 border">
                    <label class="text-danger small fw-bold d-block text-uppercase"><i class="bi bi-shield-exclamation me-1"></i> Safety Notes & Blockers:</label>
                    <p class="text-dark small mb-0">{{ $dpr->issues_safety_notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Measured Activities -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-list-check text-success me-2"></i> Measured Line Activities
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>Activity</th>
                                <th>Location</th>
                                <th class="text-end">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dpr->activities as $act)
                            <tr>
                                <td>
                                    <strong class="text-dark d-block small">{{ $act->activity_name }}</strong>
                                    @if($act->remarks)
                                    <small class="text-muted">{{ $act->remarks }}</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $act->location ?? 'Site' }}</small>
                                </td>
                                <td class="text-end font-monospace fw-bold text-dark">
                                    {{ number_format($act->quantity_completed, 2) }} {{ $act->unit }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    No specific line items measured today.
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
