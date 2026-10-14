@extends('layouts.app')

@section('title', 'Material Requisitions (MR)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Material Requisitions (MR)</h4>
        <p class="text-muted small mb-0">Site engineer material requisitions and multi-level approval tracking</p>
    </div>
    @can('mr.create')
    <a href="{{ route('material-requisitions.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>New Requisition (MR)</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('material-requisitions.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted">Approval Status Filter</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ $status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="pm_approved" {{ $status == 'pm_approved' ? 'selected' : '' }}>PM Approved</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">MR Number</th>
                        <th>Project</th>
                        <th>Requisition Date</th>
                        <th>Required By</th>
                        <th>Total Items</th>
                        <th>Submitted By</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $mr)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('material-requisitions.show', $mr) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $mr->requisition_no }}
                            </a>
                        </td>
                        <td>{{ $mr->project?->name ?? 'N/A' }}</td>
                        <td>{{ $mr->requisition_date?->format('d-m-Y') }}</td>
                        <td>{{ $mr->required_by_date?->format('d-m-Y') ?? '—' }}</td>
                        <td><span class="badge bg-secondary rounded-pill">{{ $mr->items->count() }} Items</span></td>
                        <td>{{ $mr->submittedBy?->name ?? 'System' }}</td>
                        <td>
                            @if($mr->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Approved</span>
                            @elseif($mr->status === 'pm_approved')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">PM Approved</span>
                            @elseif($mr->status === 'submitted')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Submitted</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">{{ ucfirst($mr->status) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('material-requisitions.show', $mr) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No material requisitions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requisitions->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $requisitions->links() }}
    </div>
    @endif
</div>
@endsection
