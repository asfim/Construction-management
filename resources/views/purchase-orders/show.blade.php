@extends('layouts.app')

@section('title', 'Purchase Order: ' . $purchaseOrder->po_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <h4 class="fw-bold mb-1">Purchase Order: {{ $purchaseOrder->po_number }}</h4>
        <p class="text-muted small mb-0">PO Number: {{ $purchaseOrder->po_number }} | Status: {{ ucfirst($purchaseOrder->status) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>

        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> Print PO (A4)
        </button>

        @can('po.approve')
            @if($purchaseOrder->status === 'draft')
            <form action="{{ route('purchase-orders.approve', $purchaseOrder) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this purchase order?')">
                    <i class="bi bi-check-circle me-1"></i> Approve PO
                </button>
            </form>
            @endif
        @endcan

        @can('grn.create')
            @if(in_array($purchaseOrder->status, ['approved', 'partially_received']))
            <a href="{{ route('goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-down me-1"></i> Receive Goods (Create GRN)
            </a>
            @endif
        @endcan
    </div>
</div>

<!-- Print-Ready Purchase Order Document -->
<div class="card card-custom p-4 p-md-5 bg-white border">
    <!-- Print Header with Admin Company Bindings -->
    <x-print-header 
        :company="$purchaseOrder->company" 
        title="Purchase Order" 
        subtitle="Official Procurement Contract & Material Supply Order"
        :docNumber="$purchaseOrder->po_number"
        docNumberLabel="PO Number"
        :docDate="$purchaseOrder->po_date?->format('d-m-Y')"
        docDateLabel="PO Date"
        :status="ucfirst(str_replace('_', ' ', $purchaseOrder->status))"
        :statusClass="$purchaseOrder->status === 'received' ? 'bg-success' : ($purchaseOrder->status === 'partially_received' ? 'bg-warning text-dark' : ($purchaseOrder->status === 'approved' ? 'bg-primary' : 'bg-secondary'))"
        :project="$purchaseOrder->project"
    />

    <!-- Vendor and Project info -->
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Vendor / Supplier</h6>
            <div class="fw-bold fs-6 text-dark">{{ $purchaseOrder->supplier?->name }}</div>
            <div class="text-muted small">{{ $purchaseOrder->supplier?->company_name }}</div>
            <div class="text-muted small">Phone: {{ $purchaseOrder->supplier?->phone }}</div>
            <div class="text-muted small">Address: {{ $purchaseOrder->supplier?->address }}</div>
            <div class="text-muted small">BIN: {{ $purchaseOrder->supplier?->bin_number ?? '—' }}</div>
        </div>
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Delivery & Project Destination</h6>
            <div class="fw-bold fs-6 text-dark">{{ $purchaseOrder->project?->name }} ({{ $purchaseOrder->project?->code }})</div>
            <div class="text-muted small">Delivery Address: {{ $purchaseOrder->delivery_address ?: $purchaseOrder->project?->location }}</div>
            <div class="text-muted small">Expected Delivery Date: {{ $purchaseOrder->expected_delivery_date?->format('d-m-Y') ?? '—' }}</div>
            <div class="text-muted small">Payment Terms: {{ $purchaseOrder->payment_terms ?? 'Credit' }}</div>
        </div>
    </div>

    <!-- PO Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 40%;">Item Description & Specification</th>
                    <th style="width: 15%;" class="text-end">Ordered Qty</th>
                    <th style="width: 15%;" class="text-end">Received Qty</th>
                    <th style="width: 12%;" class="text-end">Unit Price ({{ $purchaseOrder->company?->currency_symbol ?? '৳' }})</th>
                    <th style="width: 13%;" class="text-end">Total Amount ({{ $purchaseOrder->company?->currency_symbol ?? '৳' }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <div class="fw-bold">{{ $item->item?->name }}</div>
                        <small class="text-muted">{{ $item->item?->code }} | {{ $item->item?->category?->name ?? 'Material' }}</small>
                    </td>
                    <td class="text-end fw-bold">{{ number_format($item->quantity_ordered, 4) }} {{ $item->item?->unit?->symbol }}</td>
                    <td class="text-end {{ $item->quantity_received > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                        {{ number_format($item->quantity_received, 4) }} {{ $item->item?->unit?->symbol }}
                    </td>
                    <td class="text-end">{{ $purchaseOrder->company?->currency_symbol ?? '৳' }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">{{ $purchaseOrder->company?->currency_symbol ?? '৳' }} {{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" rowspan="3" class="align-top bg-light">
                        <small class="fw-bold text-muted d-block mb-1">Terms & Conditions / Notes:</small>
                        <p class="small text-muted mb-0">{{ $purchaseOrder->notes ?: 'All materials delivered to site must be accompanied by certified test certificates, valid challan, and weight bridge slips.' }}</p>
                    </td>
                    <td class="text-end fw-bold">Subtotal:</td>
                    <td class="text-end fw-bold">{{ $purchaseOrder->company?->currency_symbol ?? '৳' }} {{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-end fw-bold">VAT ({{ $purchaseOrder->vat_percentage }}%):</td>
                    <td class="text-end fw-bold text-danger">{{ $purchaseOrder->company?->currency_symbol ?? '৳' }} {{ number_format($purchaseOrder->vat_amount, 2) }}</td>
                </tr>
                <tr class="table-primary">
                    <td class="text-end fw-bold fs-6">Grand Total:</td>
                    <td class="text-end fw-bold fs-6">{{ $purchaseOrder->company?->currency_symbol ?? '৳' }} {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Official Signatures -->
    <x-print-footer 
        :company="$purchaseOrder->company"
        :preparedBy="$purchaseOrder->creator?->name ?? 'Procurement Officer'"
        preparedTitle="Procurement Officer"
        :preparedDate="$purchaseOrder->created_at?->format('d-m-Y')"
        :checkedBy="$purchaseOrder->approver?->name ?? 'Project Manager'"
        checkedTitle="Project Manager"
        :checkedDate="$purchaseOrder->approved_at?->format('d-m-Y')"
        approvedBy="Managing Director / Authorized Signatory"
        approvedTitle="Executive Approval"
        :showWatermark="true"
    />
</div>
@endsection
