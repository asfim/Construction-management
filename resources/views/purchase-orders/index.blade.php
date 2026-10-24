@extends('layouts.app')

@section('title', 'Purchase Orders (PO)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Purchase Orders (PO)</h4>
        <p class="text-muted small mb-0">Supplier purchase agreements, pricing, VAT, delivery terms and fulfillment tracking</p>
    </div>
    @can('po.create')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>Create Purchase Order (PO)</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- PO Approval Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ in_array($status, ['unapproved', 'draft']) ? 'active' : '' }}" 
                       href="{{ route('purchase-orders.index', ['status' => 'draft']) }}">
                        <i class="bi bi-clock-history me-1"></i>
                        <span>Pending Approval (Draft)</span>
                        <span class="badge {{ in_array($status, ['unapproved', 'draft']) ? 'bg-white text-primary' : 'bg-warning-subtle text-warning-emphasis' }} ms-1 rounded-pill">
                            {{ $counts['unapproved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'approved' ? 'active' : '' }}" 
                       href="{{ route('purchase-orders.index', ['status' => 'approved']) }}">
                        <i class="bi bi-check2-circle me-1"></i>
                        <span>Approved & Active</span>
                        <span class="badge {{ $status === 'approved' ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('purchase-orders.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Orders</span>
                        <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Specific Filter Dropdown -->
            <form method="GET" action="{{ route('purchase-orders.index') }}" class="d-flex align-items-center gap-2">
                <label class="form-label small text-muted mb-0 text-nowrap">Filter By:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="draft" {{ in_array($status, ['draft', 'unapproved']) ? 'selected' : '' }}>Pending Approval (Draft)</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="partially_received" {{ $status == 'partially_received' ? 'selected' : '' }}>Partially Received</option>
                    <option value="received" {{ $status == 'received' ? 'selected' : '' }}>Fully Received</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                </select>
                @if(! in_array($status, ['unapproved', 'draft']))
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset to Pending Approval">
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
                        <th class="ps-4">PO Number</th>
                        <th>Project</th>
                        <th>Supplier</th>
                        <th>PO Date</th>
                        <th class="text-end">Total Amount (৳)</th>
                        <th>Status</th>
                        <th>Approved By</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="fw-bold text-primary text-decoration-none">
                                {{ $po->po_number }}
                            </a>
                        </td>
                        <td>{{ $po->project?->name ?? 'N/A' }}</td>
                        <td>
                            <div class="fw-bold">{{ $po->supplier?->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $po->supplier?->contact_person }}</small>
                        </td>
                        <td>{{ $po->po_date?->format('d-m-Y') }}</td>
                        <td class="text-end fw-bold">৳ {{ number_format($po->total_amount, 2) }}</td>
                        <td>
                            @if($po->status === 'received')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Fully Received</span>
                            @elseif($po->status === 'partially_received')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Partially Received</span>
                            @elseif($po->status === 'approved')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Approved</span>
                            @elseif($po->status === 'draft')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Draft (Pending Approval)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ ucfirst($po->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $po->approver?->name ?? '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            @if(in_array($status, ['unapproved', 'draft']))
                                <span>No purchase orders currently pending approval.</span>
                                <div class="mt-2">
                                    <a href="{{ route('purchase-orders.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Orders ({{ $counts['all'] }})
                                    </a>
                                </div>
                            @elseif($status === 'approved')
                                <span>No approved purchase orders found.</span>
                            @else
                                <span>No purchase orders found.</span>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($purchaseOrders->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $purchaseOrders->links() }}
    </div>
    @endif
</div>
@endsection
