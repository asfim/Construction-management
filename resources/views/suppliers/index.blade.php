@extends('layouts.app')

@section('title', __('erp.nav.suppliers'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.suppliers') }} (Vendor Master)</h4>
        <p class="text-muted small mb-0">অনুমোদিত মালামাল সরবরাহকারী, কর আইনি প্রোফাইল ও ক্রেডিট শর্তাবলি</p>
    </div>
    @can('supplier.manage')
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>নতুন সরবরাহকারী যোগ করুন</span>
    </a>
    @endcan
</div>

<!-- Search Bar -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="কোড, নাম, মোবাইল অথবা ভ্যাট বিন অনুসন্ধান..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> অনুসন্ধান
                </button>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Suppliers Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>কোড</th>
                        <th>সরবরাহকারীর নাম</th>
                        <th>দায়িত্বপ্রাপ্ত ব্যক্তি ও যোগাযোগ</th>
                        <th>ট্যাক্স ও ভ্যাট (BIN/TIN)</th>
                        <th>ব্যাংক ও পেমেন্ট শর্ত</th>
                        <th>অবস্থা</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $supplier->code }}</span>
                        </td>
                        <td>
                            <a href="{{ route('suppliers.show', $supplier) }}" class="fw-bold text-dark text-decoration-none">
                                {{ $supplier->name }}
                            </a>
                            <small class="text-muted d-block">{{ $supplier->city }}</small>
                        </td>
                        <td>
                            <span class="text-dark small fw-semibold">{{ $supplier->contact_person ?? '—' }}</span>
                            <small class="text-muted d-block"><i class="bi bi-telephone me-1"></i> {{ $supplier->phone ?? '—' }}</small>
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>BIN:</strong> {{ $supplier->bin ?? '—' }}</small>
                            <small class="d-block text-muted"><strong>TIN:</strong> {{ $supplier->tin ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $supplier->payment_terms_days }} দিনের ক্রেডিট</span>
                            @if($supplier->bank_name)
                            <small class="text-muted d-block">{{ $supplier->bank_name }}</small>
                            @endif
                        </td>
                        <td>
                            @if($supplier->is_active)
                            <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> বিবরণ
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            কোনো সরবরাহকারী পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
