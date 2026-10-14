@extends('layouts.app')

@section('title', 'Goods Receipt Note: ' . $goodsReceipt->grn_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <h4 class="fw-bold mb-1">Goods Receipt Note: {{ $goodsReceipt->grn_number }}</h4>
        <p class="text-muted small mb-0">GRN: {{ $goodsReceipt->grn_number }} | Challan: {{ $goodsReceipt->delivery_challan_no }} | Warehouse: {{ $goodsReceipt->warehouse?->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>

        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> Print Certificate (A4)
        </button>

        @can('grn.approve')
            @if($goodsReceipt->status === 'draft')
            <form action="{{ route('goods-receipts.approve', $goodsReceipt) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this GRN and post items to warehouse inventory stock?')">
                    <i class="bi bi-check2-circle me-1"></i> Approve & Post Stock
                </button>
            </form>
            @endif
        @endcan
    </div>
</div>

<div class="card card-custom p-4 p-md-5 bg-white border">
    <!-- Header -->
    <div class="row align-items-start border-bottom pb-4 mb-4">
        <div class="col-6">
            <h3 class="fw-bold text-primary mb-1">{{ $goodsReceipt->company?->name ?? 'H Tech Provision Construction Ltd.' }}</h3>
            <p class="text-muted small mb-1">Project: {{ $goodsReceipt->project?->name ?? 'N/A' }} ({{ $goodsReceipt->project?->code }})</p>
            <p class="text-muted small mb-0">Warehouse: <strong>{{ $goodsReceipt->warehouse?->name }}</strong></p>
        </div>
        <div class="col-6 text-end">
            <h4 class="fw-bold text-dark text-uppercase tracking-wider">Goods Receipt Note</h4>
            <table class="table table-sm table-borderless d-inline-block text-end w-auto mb-0">
                <tr>
                    <td class="text-muted small pe-2">GRN Number:</td>
                    <td class="fw-bold">{{ $goodsReceipt->grn_number }}</td>
                </tr>
                <tr>
                    <td class="text-muted small pe-2">Date Received:</td>
                    <td class="fw-bold">{{ $goodsReceipt->received_date?->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted small pe-2">Challan Number:</td>
                    <td class="fw-bold text-primary">{{ $goodsReceipt->delivery_challan_no }}</td>
                </tr>
                <tr>
                    <td class="text-muted small pe-2">Status:</td>
                    <td>
                        @if($goodsReceipt->status === 'approved')
                            <span class="badge bg-success">Approved & Posted</span>
                        @else
                            <span class="badge bg-warning text-dark">Draft / Inspection</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- PO and Supplier Details -->
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Supplier Details</h6>
            <div class="fw-bold fs-6">{{ $goodsReceipt->purchaseOrder?->supplier?->name }}</div>
            <div class="text-muted small">{{ $goodsReceipt->purchaseOrder?->supplier?->company_name }}</div>
            <div class="text-muted small">Phone: {{ $goodsReceipt->purchaseOrder?->supplier?->phone }}</div>
            <div class="text-muted small">Challan No.: <strong>{{ $goodsReceipt->delivery_challan_no }}</strong> | Invoice No.: {{ $goodsReceipt->supplier_invoice_no ?? '—' }}</div>
        </div>
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">PO & Receiving Details</h6>
            <div class="fw-bold fs-6">
                Purchase Order: <a href="{{ route('purchase-orders.show', $goodsReceipt->purchase_order_id) }}" class="text-decoration-none">{{ $goodsReceipt->purchaseOrder?->po_number }}</a>
            </div>
            <div class="text-muted small">Received By: {{ $goodsReceipt->receiver?->name ?? 'Storekeeper' }}</div>
            <div class="text-muted small">Verified By: {{ $goodsReceipt->verifier?->name ?? 'Pending' }}</div>
            <div class="text-muted small">Verification Date: {{ $goodsReceipt->verified_at?->format('d-m-Y h:i A') ?? '—' }}</div>
        </div>
    </div>

    <!-- GRN Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 35%;">Material / Specification</th>
                    <th style="width: 15%;" class="text-end">Challan Qty</th>
                    <th style="width: 15%;" class="text-end">Received Qty</th>
                    <th style="width: 15%;" class="text-end">Accepted Qty</th>
                    <th style="width: 15%;" class="text-end">Rejected Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($goodsReceipt->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->item?->name }}</div>
                        <small class="text-muted">{{ $item->item?->code }} ({{ $item->item?->unit?->symbol }})</small>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->quantity_challan, 4) }}</td>
                    <td class="text-end">{{ number_format($item->quantity_received, 4) }}</td>
                    <td class="text-end text-success fw-bold">{{ number_format($item->quantity_accepted, 4) }}</td>
                    <td class="text-end {{ $item->quantity_rejected > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                        {{ number_format($item->quantity_rejected, 4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($goodsReceipt->notes)
    <div class="card card-custom p-3 bg-light mb-4">
        <small class="fw-bold text-muted d-block">Inspection Report & Notes:</small>
        <p class="small text-muted mb-0">{{ $goodsReceipt->notes }}</p>
    </div>
    @endif

    <!-- Signatures -->
    <div class="row pt-5 mt-4 text-center">
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $goodsReceipt->receiver?->name ?? 'Storekeeper' }}</p>
                <small class="text-muted">Received By (Storekeeper)</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">Quality Inspector / Site Engineer</p>
                <small class="text-muted">QA/QC Inspection Lead</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $goodsReceipt->verifier?->name ?? 'Project Manager' }}</p>
                <small class="text-muted">Authorized Approver</small>
            </div>
        </div>
    </div>
</div>
@endsection
