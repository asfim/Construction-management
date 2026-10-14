@extends('layouts.app')

@section('title', 'পণ্য প্রাপ্তি নোট (Goods Receipts - GRN)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">পণ্য প্রাপ্তি ব্যবস্থাপনা (Goods Receipts - GRN)</h4>
        <p class="text-muted small mb-0">সাইট ও সেন্ট্রাল গোডাউনে চালান যাচাই, গুণগত মান পরীক্ষা ও স্টক ইনভেন্টরি এন্ট্রি</p>
    </div>
    @can('grn.create')
    <a href="{{ route('goods-receipts.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-box-seam"></i>
        <span>নতুন GRN গ্রহণ করুন (New GRN)</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('goods-receipts.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <label class="form-label small text-muted">স্ট্যাটাস ফিল্টার</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">সকল স্ট্যাটাস (All)</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>ড্রাফট / পরিদর্শনাধীন (Draft)</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>অনুমোদিত ও স্টকযুক্ত (Approved & Posted)</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>প্রত্যাখ্যাত (Rejected)</option>
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
                        <th class="ps-4">GRN নম্বর</th>
                        <th>চালান নম্বর</th>
                        <th>ক্রয়াদেশ (PO)</th>
                        <th>গোডাউন (Warehouse)</th>
                        <th>প্রাপ্তির তারিখ</th>
                        <th>গ্রহীতা (Storekeeper)</th>
                        <th>অবস্থা (Status)</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
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
                                    <i class="bi bi-check-circle me-1"></i> অনুমোদিত ও স্টকযুক্ত
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                    পরিদর্শনাধীন (Draft)
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('goods-receipts.show', $grn) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> বিস্তারিত
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো পণ্য প্রাপ্তি নোট (GRN) পাওয়া যায়নি।
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
