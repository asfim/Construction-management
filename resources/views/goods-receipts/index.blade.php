@extends('layouts.app')

@section('title', 'Goods Receipts (GRN)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Goods Receipts Note (GRN)</h4>
        <p class="text-muted small mb-0">Challan verification, quality inspection, and warehouse stock receipt entry</p>
    </div>
    @can('grn.create')
    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        <span>Receive Goods (New GRN)</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- GRN Approval Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ in_array($status, ['unapproved', 'draft']) ? 'active' : '' }}" 
                       href="{{ route('goods-receipts.index', ['status' => 'draft']) }}">
                        <i class="bi bi-clock-history me-1"></i>
                        <span>Pending Inspection & Approval</span>
                        <span class="badge {{ in_array($status, ['unapproved', 'draft']) ? 'bg-white text-primary' : 'bg-warning-subtle text-warning-emphasis' }} ms-1 rounded-pill">
                            {{ $counts['unapproved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'approved' ? 'active' : '' }}" 
                       href="{{ route('goods-receipts.index', ['status' => 'approved']) }}">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span>Approved & Posted</span>
                        <span class="badge {{ $status === 'approved' ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('goods-receipts.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Goods Receipts</span>
                        <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Specific Filter Dropdown -->
            <form method="GET" action="{{ route('goods-receipts.index') }}" class="d-flex align-items-center gap-2">
                <label class="form-label small text-muted mb-0 text-nowrap">Filter By:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="draft" {{ in_array($status, ['draft', 'unapproved']) ? 'selected' : '' }}>Pending Inspection (Draft)</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved & Stock Posted</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                </select>
                @if(! in_array($status, ['unapproved', 'draft']))
                <a href="{{ route('goods-receipts.index') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset to Pending Inspection">
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
                        <th class="ps-4">GRN Number</th>
                        <th>Challan No.</th>
                        <th>Purchase Order (PO)</th>
                        <th>Warehouse / Store</th>
                        <th>Received Date</th>
                        <th>Receiver (Storekeeper)</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goodsReceipts as $grn)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('goods-receipts.show', $grn) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $grn->grn_number }}
                            </a>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $grn->delivery_challan_no }}</span></td>
                        <td>{{ $grn->purchaseOrder?->po_number ?? 'N/A' }}</td>
                        <td>{{ $grn->warehouse?->name ?? 'N/A' }}</td>
                        <td>{{ $grn->received_date?->format('d-m-Y') }}</td>
                        <td>{{ $grn->receiver?->name ?? 'System' }}</td>
                        <td>
                            @if($grn->status === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle me-1"></i> Approved & Posted
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-clock me-1"></i> Draft (Inspection Pending)
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('goods-receipts.show', $grn) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            @if(in_array($status, ['unapproved', 'draft']))
                                <span>No goods receipt notes currently pending inspection or approval.</span>
                                <div class="mt-2">
                                    <a href="{{ route('goods-receipts.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Goods Receipts ({{ $counts['all'] }})
                                    </a>
                                </div>
                            @elseif($status === 'approved')
                                <span>No approved goods receipt notes found.</span>
                            @else
                                <span>No goods receipt notes found.</span>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($goodsReceipts->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $goodsReceipts->links() }}
    </div>
    @endif
</div>
@endsection
