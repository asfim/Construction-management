@extends('layouts.app')

@section('title', 'BOQ Revisions & Variations — ' . $project->name)

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
            <span class="badge badge-status badge-{{ $project->status }}">{{ __('erp.status.' . $project->status) }}</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">BOQ & Estimation Revisions</h4>
        <p class="text-muted small mb-0">{{ $project->name }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Project</span>
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Version No</th>
                        <th>Title & Remarks</th>
                        <th>Total Items</th>
                        <th>Total BOQ Amount (BDT)</th>
                        <th>Approval & Lock Status</th>
                        <th>Prepared / Approved By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boqVersions as $ver)
                    <tr>
                        <td>
                            <span class="badge bg-primary fs-6 px-3 py-2">Version {{ $ver->version_number }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $ver->title }}</strong>
                            <small class="text-muted">{{ $ver->remarks ?? 'No remarks provided' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $ver->items_count }} Items</span>
                        </td>
                        <td class="fw-bold fs-6 text-dark">
                            ৳ {{ number_format($ver->total_amount, 2) }}
                        </td>
                        <td>
                            @if($ver->is_locked)
                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                <i class="bi bi-lock-fill me-1"></i> Approved & Locked (Immutable)
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2">
                                <i class="bi bi-pencil me-1"></i> Draft (Editable)
                            </span>
                            @endif
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>Created:</strong> {{ $ver->creator?->name ?? 'System' }}</small>
                            @if($ver->approved_by)
                            <small class="d-block text-success"><strong>Approved:</strong> {{ $ver->approver?->name }} ({{ $ver->approved_at?->format('d-m-Y') }})</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('projects.boqs.show', [$project, $ver]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-table me-1"></i> Open BOQ
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            No BOQ records found for this project.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
