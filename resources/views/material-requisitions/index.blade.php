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
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- Approval Workflow Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'unapproved' ? 'active' : '' }}" 
                       href="{{ route('material-requisitions.index', ['status' => 'unapproved']) }}">
                        <i class="bi bi-clock-history me-1"></i>
                        <span>Pending Approval</span>
                        <span class="badge {{ $status === 'unapproved' ? 'bg-white text-primary' : 'bg-warning-subtle text-warning-emphasis' }} ms-1 rounded-pill">
                            {{ $counts['unapproved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'approved' ? 'active' : '' }}" 
                       href="{{ route('material-requisitions.index', ['status' => 'approved']) }}">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span>Approved</span>
                        <span class="badge {{ $status === 'approved' ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('material-requisitions.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Requisitions</span>
                        <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Specific Filter Dropdown -->
            <form method="GET" action="{{ route('material-requisitions.index') }}" class="d-flex align-items-center gap-2">
                <label class="form-label small text-muted mb-0 text-nowrap">Filter By:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="unapproved" {{ $status == 'unapproved' ? 'selected' : '' }}>Pending / Unapproved</option>
                    <option value="submitted" {{ $status == 'submitted' ? 'selected' : '' }}>Submitted (Site)</option>
                    <option value="pm_approved" {{ $status == 'pm_approved' ? 'selected' : '' }}>PM Approved</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                </select>
                @if($status !== 'unapproved')
                <a href="{{ route('material-requisitions.index') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset to Pending Approval">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
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
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i> Approved
                                </span>
                            @elseif($mr->status === 'pm_approved')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="bi bi-clock me-1"></i> PM Approved
                                </span>
                            @elseif($mr->status === 'submitted')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-hourglass-split me-1"></i> Submitted
                                </span>
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
                            @if($status === 'unapproved')
                                <span>No pending material requisitions awaiting approval.</span>
                                <div class="mt-2">
                                    <a href="{{ route('material-requisitions.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Requisitions ({{ $counts['all'] }})
                                    </a>
                                </div>
                            @elseif($status === 'approved')
                                <span>No approved material requisitions found.</span>
                            @else
                                <span>No material requisitions found.</span>
                            @endif
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
