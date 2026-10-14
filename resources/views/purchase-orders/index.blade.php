@extends('layouts.app')

@section('title', 'ক্রয়াদেশ তালিকা (Purchase Orders)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">ক্রয়াদেশ ব্যবস্থাপনা (Purchase Orders)</h4>
        <p class="text-muted small mb-0">সরবরাহকারীদের সাথে চুক্তিকৃত ক্রয়াদেশ, মূল্য, ভ্যাট এবং ডেলিভারি ট্র্যাকিং</p>
    </div>
    @can('po.create')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>নতুন ক্রয়াদেশ তৈরি (Create PO)</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('purchase-orders.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted">স্ট্যাটাস ফিল্টার</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">সকল স্ট্যাটাস (All)</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>ড্রাফট (Draft)</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>অনুমোদিত (Approved)</option>
                    <option value="partially_received" {{ $status == 'partially_received' ? 'selected' : '' }}>আংশিক প্রাপ্ত (Partially Received)</option>
                    <option value="received" {{ $status == 'received' ? 'selected' : '' }}>সম্পূর্ণ প্রাপ্ত (Received)</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>বাতিল (Cancelled)</option>
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
                        <th class="ps-4">পিও নম্বর (PO No)</th>
                        <th>প্রকল্প (Project)</th>
                        <th>সরবরাহকারী (Supplier)</th>
                        <th>তারিখ</th>
                        <th class="text-end">মোট মূল্য (৳)</th>
                        <th>অবস্থা (Status)</th>
                        <th>অনুমোদনকারী</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
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
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">সম্পূর্ণ প্রাপ্ত (Received)</span>
                            @elseif($po->status === 'partially_received')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">আংশিক প্রাপ্ত</span>
                            @elseif($po->status === 'approved')
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">অনুমোদিত (Approved)</span>
                            @elseif($po->status === 'draft')
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">ড্রাফট (Draft)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ ucfirst($po->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $po->approver?->name ?? '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> বিস্তারিত
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো ক্রয়াদেশ পাওয়া যায়নি।
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
