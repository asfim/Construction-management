@extends('layouts.app')

@section('title', __('erp.nav.items'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.items') }} (Materials Master)</h4>
        <p class="text-muted small mb-0">কনস্ট্রাকশন মালামাল, স্পেসিফিকেশন ও মানদণ্ড সংগ্রহ দর তালিকা</p>
    </div>
    @can('item.manage')
    <a href="{{ route('items.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>নতুন মালামাল যোগ করুন</span>
    </a>
    @endcan
</div>

<!-- Search & Filters -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('items.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="কোড, নাম বা ব্র্যান্ড অনুসন্ধান..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">সকল ক্যাটাগরি (All Categories)</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ $cat->name_bn }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> ফিল্টার
                </button>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Items Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>কোড</th>
                        <th>মালামালের নাম ও স্পেসিফিকেশন</th>
                        <th>ক্যাটাগরি</th>
                        <th>একক (Unit)</th>
                        <th>স্ট্যান্ডার্ড রেট</th>
                        <th>মিনিমাম অ্যালার্ট স্টক</th>
                        <th>অবস্থা</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $item->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                            @if($item->name_bn)
                            <small class="text-secondary d-block">{{ $item->name_bn }}</small>
                            @endif
                            <small class="text-muted">
                                @if($item->brand) <strong>ব্র্যান্ড:</strong> {{ $item->brand }} | @endif
                                {{ $item->specification }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $item->category?->name ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $item->unit?->symbol }}</span>
                            <small class="text-muted">({{ $item->unit?->name_bn }})</small>
                        </td>
                        <td class="fw-bold text-dark">
                            ৳ {{ number_format($item->standard_rate, 2) }}
                        </td>
                        <td>
                            <span class="text-muted font-monospace">{{ number_format($item->min_stock_alert, 2) }}</span> {{ $item->unit?->symbol }}
                        </td>
                        <td>
                            @if($item->is_active)
                            <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            কোনো মালামাল পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
