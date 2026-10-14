@extends('layouts.app')

@section('title', 'উপকরণ চাহিদা বিবরণী: ' . $materialRequisition->requisition_no)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">উপকরণ চাহিদা বিবরণী: {{ $materialRequisition->requisition_no }}</h4>
        <p class="text-muted small mb-0">প্রকল্প: {{ $materialRequisition->project?->name ?? 'N/A' }} | দাখিলের তারিখ: {{ $materialRequisition->requisition_date?->format('d-m-Y') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('material-requisitions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
        </a>

        @can('mr.approve')
            @if($materialRequisition->status === 'submitted')
            <form action="{{ route('material-requisitions.approve', $materialRequisition) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-info text-white" onclick="return confirm('আপনি কি নিশ্চিত যে পিএম হিসেবে এই রিকুইজিশন অনুমোদন করতে চান?')">
                    <i class="bi bi-check-circle me-1"></i> পিএম অনুমোদন (PM Approve)
                </button>
            </form>
            @elseif($materialRequisition->status === 'pm_approved')
            <form action="{{ route('material-requisitions.approve', $materialRequisition) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('আপনি কি নিশ্চিত যে এই রিকুইজিশনটি চূড়ান্ত অনুমোদন দিতে চান?')">
                    <i class="bi bi-check2-all me-1"></i> চূড়ান্ত অনুমোদন (Final Approve)
                </button>
            </form>
            @endif
        @endcan

        @if($materialRequisition->status === 'approved')
        @can('po.create')
        <a href="{{ route('purchase-orders.create', ['mr_id' => $materialRequisition->id]) }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-1"></i> ক্রয়াদেশ তৈরি করুন (Create PO)
        </a>
        @endcan
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted d-block">অবস্থা (Status)</small>
            <div class="mt-1">
                @if($materialRequisition->status === 'approved')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">অনুমোদিত (Approved)</span>
                @elseif($materialRequisition->status === 'pm_approved')
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 fs-6">PM Approved</span>
                @elseif($materialRequisition->status === 'submitted')
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 fs-6">দাখিলকৃত (Submitted)</span>
                @else
                    <span class="badge bg-secondary px-3 py-2 fs-6">{{ ucfirst($materialRequisition->status) }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted d-block">দাখিলকারী (Submitted By)</small>
            <div class="fw-bold mt-1 fs-6">{{ $materialRequisition->submittedBy?->name ?? 'N/A' }}</div>
            <small class="text-secondary">{{ $materialRequisition->created_at->format('d-m-Y h:i A') }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted d-block">পিএম যাচাই (PM Approval)</small>
            <div class="fw-bold mt-1 fs-6">{{ $materialRequisition->pmApprovedBy?->name ?? 'অপেক্ষমান' }}</div>
            <small class="text-secondary">{{ $materialRequisition->pm_approved_at?->format('d-m-Y h:i A') ?? '—' }}</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3">
            <small class="text-muted d-block">চূড়ান্ত অনুমোদন (Procurement Head)</small>
            <div class="fw-bold mt-1 fs-6">{{ $materialRequisition->approvedBy?->name ?? 'অপেক্ষমান' }}</div>
            <small class="text-secondary">{{ $materialRequisition->approved_at?->format('d-m-Y h:i A') ?? '—' }}</small>
        </div>
    </div>
</div>

<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">চাহিদাকৃত মালামালের বিস্তারিত তালিকা</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>আইটেম ও কোড</th>
                        <th>কস্ট কোড</th>
                        <th class="text-end">চাহিদাকৃত পরিমাণ</th>
                        <th class="text-end">অনুমোদিত পরিমাণ</th>
                        <th class="text-end">অর্ডারকৃত পরিমাণ</th>
                        <th>মন্তব্য</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materialRequisition->items as $index => $item)
                    <tr>
                        <td class="ps-4">{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $item->item?->name }}</div>
                            <small class="text-muted">{{ $item->item?->code }}</small>
                        </td>
                        <td>
                            @if($item->costCode)
                                <span class="badge bg-light text-dark border">{{ $item->costCode->code }} - {{ $item->costCode->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold">{{ number_format($item->quantity_requested, 4) }} {{ $item->item?->unit?->symbol }}</td>
                        <td class="text-end text-success fw-bold">{{ number_format($item->quantity_approved, 4) }} {{ $item->item?->unit?->symbol }}</td>
                        <td class="text-end text-primary fw-bold">{{ number_format($item->quantity_ordered, 4) }} {{ $item->item?->unit?->symbol }}</td>
                        <td>{{ $item->remarks ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($materialRequisition->notes)
<div class="card card-custom">
    <div class="card-body">
        <h6 class="fw-bold text-muted mb-2">উদ্দেশ্য ও বিশেষ নির্দেশনা (Notes):</h6>
        <p class="mb-0">{{ $materialRequisition->notes }}</p>
    </div>
</div>
@endif
@endsection
