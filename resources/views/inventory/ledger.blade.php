@extends('layouts.app')

@section('title', 'Inventory & Stock Ledger')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Inventory Ledger & Stock Movement</h4>
        <p class="text-muted small mb-0">Warehouse stock balances, moving average valuation, receipts, and site issue movements</p>
    </div>
    @can('inventory.issue')
    <a href="{{ route('inventory.issues.create') }}" class="btn btn-warning text-dark d-flex align-items-center gap-2 fw-semibold">
        <i class="bi bi-box-arrow-up-right"></i>
        <span>Issue Stock to Site</span>
    </a>
    @endcan
</div>

<!-- Stock Cards / Balances Table -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-boxes me-2"></i>Current Stock Balances by Warehouse</h5>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $stockBalances->count() }} Items in Stock</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Item Name & Code</th>
                        <th>Warehouse / Store</th>
                        <th class="text-end">Current Balance</th>
                        <th class="text-end">Avg. Cost Rate (৳)</th>
                        <th class="text-end pe-4">Total Stock Valuation (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockBalances as $stock)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold">{{ $stock->item?->name }}</div>
                            <small class="text-muted">{{ $stock->item?->code }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-building me-1"></i>{{ $stock->warehouse?->name }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-success fs-6">
                            {{ number_format($stock->current_stock, 4) }} {{ $stock->item?->unit?->symbol }}
                        </td>
                        <td class="text-end">৳ {{ number_format($stock->avg_cost, 2) }}</td>
                        <td class="text-end pe-4 fw-bold text-primary fs-6">
                            ৳ {{ number_format($stock->total_valuation, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No positive stock balances recorded.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Ledger Movement Table -->
<div class="card card-custom">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Stock Movement Transactions</h5>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('inventory.ledger') }}" class="d-flex gap-2 justify-content-md-end">
                    <select name="warehouse_id" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ $selectedWarehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>

                    <select name="item_id" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="">All Materials</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" {{ $selectedItemId == $it->id ? 'selected' : '' }}>{{ $it->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Transaction Type</th>
                        <th>Material</th>
                        <th>Warehouse</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Unit Cost (৳)</th>
                        <th class="text-end">Total Valuation (৳)</th>
                        <th>Reference / Notes</th>
                        <th class="pe-4">Entered By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $txn)
                    <tr>
                        <td class="ps-4">{{ $txn->transaction_date?->format('d-m-Y') }}</td>
                        <td>
                            @if($txn->transaction_type === 'grn_in')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-arrow-down-left me-1"></i> GRN Receipt
                                </span>
                            @elseif($txn->transaction_type === 'issue_out')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-arrow-up-right me-1"></i> Site Issue
                                </span>
                            @elseif($txn->transaction_type === 'return_in')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    Stock Return
                                </span>
                            @else
                                <span class="badge bg-secondary px-2 py-1">{{ $txn->transaction_type }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold">{{ $txn->item?->name }}</div>
                            <small class="text-muted">{{ $txn->item?->code }}</small>
                        </td>
                        <td>{{ $txn->warehouse?->name }}</td>
                        <td class="text-end fw-bold {{ $txn->quantity > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $txn->quantity > 0 ? '+' : '' }}{{ number_format($txn->quantity, 4) }} {{ $txn->item?->unit?->symbol }}
                        </td>
                        <td class="text-end">৳ {{ number_format($txn->unit_cost, 2) }}</td>
                        <td class="text-end fw-bold">৳ {{ number_format($txn->total_cost, 2) }}</td>
                        <td>
                            <small class="text-muted">{{ $txn->notes ?? '—' }}</small>
                        </td>
                        <td class="pe-4">{{ $txn->creator?->name ?? 'System' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            No inventory transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
