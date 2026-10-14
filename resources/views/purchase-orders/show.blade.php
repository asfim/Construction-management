@extends('layouts.app')

@section('title', 'ক্রয়াদেশ: ' . $purchaseOrder->po_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <h4 class="fw-bold mb-1">ক্রয়াদেশ বিবরণী (Purchase Order)</h4>
        <p class="text-muted small mb-0">পিও নম্বর: {{ $purchaseOrder->po_number }} | অবস্থা: {{ ucfirst($purchaseOrder->status) }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
        </a>

        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> প্রিন্ট ক্রয়াদেশ (Print A4)
        </button>

        @can('po.approve')
            @if($purchaseOrder->status === 'draft')
            <form action="{{ route('purchase-orders.approve', $purchaseOrder) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('আপনি কি নিশ্চিত যে এই ক্রয়াদেশটি অনুমোদন করতে চান?')">
                    <i class="bi bi-check-circle me-1"></i> ক্রয়াদেশ অনুমোদন করুন (Approve PO)
                </button>
            </form>
            @endif
        @endcan

        @can('grn.create')
            @if(in_array($purchaseOrder->status, ['approved', 'partially_received']))
            <a href="{{ route('goods-receipts.create', ['po_id' => $purchaseOrder->id]) }}" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-down me-1"></i> পণ্য রিসিভ করুন (Create GRN)
            </a>
            @endif
        @endcan
    </div>
</div>

<!-- Print-Ready Purchase Order Document -->
<div class="card card-custom p-4 p-md-5 bg-white border">
    <!-- Header -->
    <div class="row align-items-start border-bottom pb-4 mb-4">
        <div class="col-6">
            <h3 class="fw-bold text-primary mb-1">{{ $purchaseOrder->company?->name ?? 'H Tech Provision IT' }}</h3>
            <p class="text-muted small mb-1">{{ $purchaseOrder->company?->address ?? 'Dhaka, Bangladesh' }}</p>
            <p class="text-muted small mb-0">BIN: {{ $purchaseOrder->company?->bin ?? '003291823-0101' }} | TIN: {{ $purchaseOrder->company?->tin ?? '492817263512' }}</p>
        </div>
        <div class="col-6 text-end">
            <h4 class="fw-bold text-dark text-uppercase tracking-wider">Purchase Order</h4>
            <table class="table table-sm table-borderless d-inline-block text-end w-auto mb-0">
                <tr>
                    <td class="text-muted small pe-2">PO Number:</td>
                    <td class="fw-bold">{{ $purchaseOrder->po_number }}</td>
                </tr>
                <tr>
                    <td class="text-muted small pe-2">PO Date:</td>
                    <td class="fw-bold">{{ $purchaseOrder->po_date?->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="text-muted small pe-2">Status:</td>
                    <td>
                        @if($purchaseOrder->status === 'received')
                            <span class="badge bg-success">Received</span>
                        @elseif($purchaseOrder->status === 'partially_received')
                            <span class="badge bg-warning text-dark">Partially Received</span>
                        @elseif($purchaseOrder->status === 'approved')
                            <span class="badge bg-primary">Approved</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Vendor and Project info -->
    <div class="row mb-4">
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Vendor / Supplier (সরবরাহকারী)</h6>
            <div class="fw-bold fs-6">{{ $purchaseOrder->supplier?->name }}</div>
            <div class="text-muted small">{{ $purchaseOrder->supplier?->company_name }}</div>
            <div class="text-muted small">ফোন: {{ $purchaseOrder->supplier?->phone }}</div>
            <div class="text-muted small">ঠিকানা: {{ $purchaseOrder->supplier?->address }}</div>
            <div class="text-muted small">BIN: {{ $purchaseOrder->supplier?->bin_number ?? '—' }}</div>
        </div>
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Delivery & Project (গন্তব্য)</h6>
            <div class="fw-bold fs-6">{{ $purchaseOrder->project?->name }} ({{ $purchaseOrder->project?->code }})</div>
            <div class="text-muted small">ডেলিভারি ঠিকানা: {{ $purchaseOrder->delivery_address ?: $purchaseOrder->project?->location }}</div>
            <div class="text-muted small">প্রত্যাশিত ডেলিভারি তারিখ: {{ $purchaseOrder->expected_delivery_date?->format('d-m-Y') ?? '—' }}</div>
            <div class="text-muted small">পেমেন্ট শর্ত: {{ $purchaseOrder->payment_terms ?? 'Credit' }}</div>
        </div>
    </div>

    <!-- PO Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 40%;">মালামালের বিবরণ (Item & Specification)</th>
                    <th style="width: 15%;" class="text-end">অর্ডারকৃত পরিমাণ</th>
                    <th style="width: 15%;" class="text-end">গৃহীত পরিমাণ (Received)</th>
                    <th style="width: 12%;" class="text-end">একক দর (৳)</th>
                    <th style="width: 13%;" class="text-end">মোট মূল্য (৳)</th>
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
                    <td class="text-end">৳ {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end fw-bold">৳ {{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" rowspan="3" class="align-top bg-light">
                        <small class="fw-bold text-muted d-block mb-1">নোট / শর্তাবলী:</small>
                        <p class="small text-muted mb-0">{{ $purchaseOrder->notes ?: 'সকল মালামাল সাইটে বুয়েট/অনুমোদিত টেস্ট রিপোর্ট ও সঠিক ওজন স্লিপ সহ সরবরাহ করতে হবে।' }}</p>
                    </td>
                    <td class="text-end fw-bold">উপ-মোট (Subtotal):</td>
                    <td class="text-end fw-bold">৳ {{ number_format($purchaseOrder->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-end fw-bold">ভ্যাট (VAT {{ $purchaseOrder->vat_percentage }}%):</td>
                    <td class="text-end fw-bold text-danger">৳ {{ number_format($purchaseOrder->vat_amount, 2) }}</td>
                </tr>
                <tr class="table-primary">
                    <td class="text-end fw-bold fs-6">সর্বমোট (Grand Total):</td>
                    <td class="text-end fw-bold fs-6">৳ {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Signatures -->
    <div class="row pt-5 mt-4 text-center">
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $purchaseOrder->creator?->name ?? 'প্রোকিউরমেন্ট অফিসার' }}</p>
                <small class="text-muted">প্রস্তুতকারী (Prepared By)</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $purchaseOrder->approver?->name ?? 'প্রজেক্ট ম্যানেজার' }}</p>
                <small class="text-muted">অনুমোদনকারী (Approved By)</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">ম্যানেজিং ডিরেক্টর / সিইও</p>
                <small class="text-muted">চূড়ান্ত স্বাক্ষর (Authorized Signature)</small>
            </div>
        </div>
    </div>
</div>
@endsection
