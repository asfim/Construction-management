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
    <div class="card-body">
        <form method="GET" action="{{ route('goods-receipts.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted">Status Filter</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft / Inspection Pending</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved & Stock Posted</option>
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
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    Draft
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
                            No goods receipts found.
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
