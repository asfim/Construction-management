@extends('layouts.app')

@section('title', 'পণ্য প্রাপ্তি নোট: ' . $goodsReceipt->grn_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
    <div>
        <h4 class="fw-bold mb-1">পণ্য প্রাপ্তি নোট বিবরণী (GRN Inspection Certificate)</h4>
        <p class="text-muted small mb-0">GRN নম্বর: {{ $goodsReceipt->grn_number }} | চালান: {{ $goodsReceipt->delivery_challan_no }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('goods-receipts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
        </a>

        <button onclick="window.print()" class="btn btn-outline-dark">
            <i class="bi bi-printer me-1"></i> প্রিন্ট সার্টিফিকেট (Print A4)
        </button>

        @can('grn.approve')
            @if($goodsReceipt->status === 'draft')
            <form action="{{ route('goods-receipts.approve', $goodsReceipt) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('আপনি কি নিশ্চিত যে এই চালানটি অনুমোদন করে ইনভেন্টরি স্টকে যুক্ত করতে চান?')">
                    <i class="bi bi-check2-circle me-1"></i> অনুমোদন ও গোডাউন স্টকে পোস্টিং করুন (Approve & Post)
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
            <h3 class="fw-bold text-primary mb-1">{{ $goodsReceipt->company?->name ?? 'H Tech Provision IT' }}</h3>
            <p class="text-muted small mb-1">প্রকল্প: {{ $goodsReceipt->project?->name ?? 'N/A' }} ({{ $goodsReceipt->project?->code }})</p>
            <p class="text-muted small mb-0">গোডাউন: <strong>{{ $goodsReceipt->warehouse?->name }}</strong></p>
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
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">Supplier Details (সরবরাহকারী)</h6>
            <div class="fw-bold fs-6">{{ $goodsReceipt->purchaseOrder?->supplier?->name }}</div>
            <div class="text-muted small">{{ $goodsReceipt->purchaseOrder?->supplier?->company_name }}</div>
            <div class="text-muted small">ফোন: {{ $goodsReceipt->purchaseOrder?->supplier?->phone }}</div>
            <div class="text-muted small">চালান নং: <strong>{{ $goodsReceipt->delivery_challan_no }}</strong> | ইনভয়েস নং: {{ $goodsReceipt->supplier_invoice_no ?? '—' }}</div>
        </div>
        <div class="col-6">
            <h6 class="fw-bold text-uppercase text-secondary small mb-2">PO & Receiving Details</h6>
            <div class="fw-bold fs-6">
                ক্রয়াদেশ: <a href="{{ route('purchase-orders.show', $goodsReceipt->purchase_order_id) }}" class="text-decoration-none">{{ $goodsReceipt->purchaseOrder?->po_number }}</a>
            </div>
            <div class="text-muted small">রিসিভকারী: {{ $goodsReceipt->receiver?->name ?? 'Storekeeper' }}</div>
            <div class="text-muted small">যাচাইকারী: {{ $goodsReceipt->verifier?->name ?? 'Pending' }}</div>
            <div class="text-muted small">যাচাইয়ের সময়: {{ $goodsReceipt->verified_at?->format('d-m-Y h:i A') ?? '—' }}</div>
        </div>
    </div>

    <!-- GRN Items Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 35%;">মালামাল ও বিবরণ (Material Specification)</th>
                    <th style="width: 15%;" class="text-end">চালান পরিমাণ</th>
                    <th style="width: 15%;" class="text-end">প্রাপ্ত পরিমাণ</th>
                    <th style="width: 15%;" class="text-end">গৃহীত (Accepted)</th>
                    <th style="width: 15%;" class="text-end">প্রত্যাখ্যাত (Rejected)</th>
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
        <small class="fw-bold text-muted d-block">পরিদর্শন রিপোর্ট ও বিশেষ মন্তব্য:</small>
        <p class="small text-muted mb-0">{{ $goodsReceipt->notes }}</p>
    </div>
    @endif

    <!-- Signatures -->
    <div class="row pt-5 mt-4 text-center">
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $goodsReceipt->receiver?->name ?? 'স্টোরকিপার' }}</p>
                <small class="text-muted">পণ্য গ্রহীতা (Received By Storekeeper)</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">কোয়ালিটি ইন্সপেক্টর / সাইট ইঞ্জিনিয়ার</p>
                <small class="text-muted">গুণগত মান যাচাইকারী (QA/QC Inspector)</small>
            </div>
        </div>
        <div class="col-4">
            <div class="border-top pt-2 mx-3">
                <p class="fw-bold mb-0">{{ $goodsReceipt->verifier?->name ?? 'প্রজেক্ট ম্যানেজার' }}</p>
                <small class="text-muted">চূড়ান্ত অনুমোদন (Project Manager / Approver)</small>
            </div>
        </div>
    </div>
</div>
@endsection
