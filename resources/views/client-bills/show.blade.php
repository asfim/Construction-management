@extends('layouts.app')

@section('title', "RA Bill: {$clientBill->bill_number}")

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace fs-6">{{ $clientBill->bill_number }}</span>
            @if($clientBill->status === 'paid')
            <span class="badge bg-success text-white">Paid</span>
            @elseif($clientBill->status === 'certified')
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                <i class="bi bi-patch-check-fill me-1"></i> Certified
            </span>
            @else
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <i class="bi bi-clock me-1"></i> Submitted
            </span>
            @endif
        </div>
        <h4 class="fw-bold mb-1 text-dark">Running Account Bill & Interim Certificate</h4>
        <p class="text-muted small mb-0">{{ $clientBill->project?->name }} | Client: {{ $clientBill->project?->client_name ?? 'Client' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span>Print Certificate</span>
        </button>

        @can('bill.certify')
        @if($clientBill->status === 'submitted')
        <form action="{{ route('client-bills.certify', $clientBill) }}" method="POST" onsubmit="return confirm('Certify this Running Account bill?');">
            @csrf
            <button type="submit" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-patch-check"></i>
                <span>Certify Bill</span>
            </button>
        </form>
        @elseif($clientBill->status === 'certified')
        <form action="{{ route('client-bills.mark-paid', $clientBill) }}" method="POST" onsubmit="return confirm('Mark this Running Account bill as paid?');">
            @csrf
            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-cash-stack"></i>
                <span>Mark Paid</span>
            </button>
        </form>
        @endif
        @endcan

        <a href="{{ route('client-bills.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Bills</span>
        </a>
    </div>
</div>

<!-- Financial Summary Strip -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Gross Measured Amount</span>
            <h4 class="fw-bold text-dark font-monospace mb-0">৳ {{ number_format($clientBill->gross_amount, 2) }}</h4>
            <small class="text-muted">Total line items value</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Advance Recovery</span>
            <h4 class="fw-bold text-danger font-monospace mb-0">- ৳ {{ number_format($clientBill->advance_recovery_amount, 2) }}</h4>
            <small class="text-muted">Rate: {{ number_format($clientBill->advance_recovery_percentage, 1) }}%</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Retention Withheld</span>
            <h4 class="fw-bold text-warning font-monospace mb-0">- ৳ {{ number_format($clientBill->retention_amount, 2) }}</h4>
            <small class="text-muted">Rate: {{ number_format($clientBill->retention_percentage, 1) }}%</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100 bg-success-subtle border-success-subtle">
            <span class="text-success small fw-semibold d-block mb-1">Net Certified Amount</span>
            <h3 class="fw-bold text-success font-monospace mb-0">৳ {{ number_format($clientBill->net_payable, 2) }}</h3>
            <small class="text-success">Payable to Contractor</small>
        </div>
    </div>
</div>

<!-- Measured Line Items Detail Table -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i> Certified Measurement Lines
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Item Code & Description</th>
                        <th>Unit</th>
                        <th class="text-end">Contract Rate (৳)</th>
                        <th class="text-end">Previous Qty</th>
                        <th class="text-end">Current Qty</th>
                        <th class="text-end">Cumulative Qty</th>
                        <th class="text-end">Current Amount (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientBill->items as $item)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $item->boqItem?->item_code ?? 'ITEM' }}</span>
                            <span class="text-dark small fw-medium ms-2">{{ $item->description }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $item->unit }}</span>
                        </td>
                        <td class="text-end font-monospace text-dark">
                            ৳ {{ number_format($item->unit_rate, 2) }}
                        </td>
                        <td class="text-end font-monospace text-muted">
                            {{ number_format($item->previous_quantity, 4) }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-primary">
                            {{ number_format($item->current_quantity, 4) }}
                        </td>
                        <td class="text-end font-monospace text-dark">
                            {{ number_format($item->total_quantity, 4) }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark">
                            ৳ {{ number_format($item->current_amount, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light fw-bold font-monospace">
                    <tr>
                        <td colspan="6" class="text-end">Gross Measured Work:</td>
                        <td class="text-end text-dark">৳ {{ number_format($clientBill->gross_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end text-danger">Less: Advance Mobilization Recovery ({{ $clientBill->advance_recovery_percentage }}%):</td>
                        <td class="text-end text-danger">- ৳ {{ number_format($clientBill->advance_recovery_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end text-warning">Less: Security Retention Money ({{ $clientBill->retention_percentage }}%):</td>
                        <td class="text-end text-warning">- ৳ {{ number_format($clientBill->retention_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end text-success">Add: VAT ({{ $clientBill->vat_percentage }}%):</td>
                        <td class="text-end text-success">+ ৳ {{ number_format($clientBill->vat_amount, 2) }}</td>
                    </tr>
                    <tr class="table-primary fs-6">
                        <td colspan="6" class="text-end">Net Certified Amount:</td>
                        <td class="text-end text-primary">৳ {{ number_format($clientBill->net_payable, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Signoff / Certification Block -->
<div class="row g-4">
    <div class="col-12 col-md-4 text-center">
        <div class="p-4 bg-light rounded-3 border">
            <small class="text-muted d-block mb-4">Prepared By (Quantity Surveyor)</small>
            <strong class="text-dark d-block">{{ $clientBill->creator?->name ?? 'QS Staff' }}</strong>
            <small class="text-muted">{{ $clientBill->created_at->format('d-m-Y') }}</small>
        </div>
    </div>
    <div class="col-12 col-md-4 text-center">
        <div class="p-4 bg-light rounded-3 border">
            <small class="text-muted d-block mb-4">Checked By (Project Manager)</small>
            <strong class="text-dark d-block">{{ $clientBill->project?->projectManager?->name ?? 'Project Manager' }}</strong>
            <small class="text-muted">Operations Verification</small>
        </div>
    </div>
    <div class="col-12 col-md-4 text-center">
        <div class="p-4 bg-light rounded-3 border">
            <small class="text-muted d-block mb-4">Certified By (Client / Consultant)</small>
            <strong class="text-dark d-block">{{ $clientBill->certifier?->name ?? ($clientBill->status === 'certified' || $clientBill->status === 'paid' ? 'Consultant Representative' : 'Awaiting Certification') }}</strong>
            <small class="text-muted">{{ $clientBill->certified_at ? $clientBill->certified_at->format('d-m-Y') : 'Pending' }}</small>
        </div>
    </div>
</div>
@endsection
