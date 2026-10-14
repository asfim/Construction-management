@extends('layouts.app')

@section('title', 'গোডাউন ও সাইট স্টোর (Warehouses & Site Stores)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">গোডাউন ও সাইট স্টোর ব্যবস্থাপনা (Warehouses & Site Stores)</h4>
        <p class="text-muted small mb-0">সেন্ট্রাল ইয়ার্ড ও প্রকল্পভিত্তিক সাইট স্টোরের তালিকা ও স্টোরকিপার বরাদ্দ</p>
    </div>
    @can('warehouse.manage')
    <a href="{{ route('warehouses.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>নতুন গোডাউন যোগ করুন</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('warehouses.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="গোডাউনের নাম, কোড বা অবস্থান দিয়ে খুঁজুন..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> খুঁজুন</button>
                    @if($search)
                    <a href="{{ route('warehouses.index') }}" class="btn btn-outline-danger">রিসেট</a>
                    @endif
                </div>
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
                        <th class="ps-4">কোড</th>
                        <th>গোডাউনের নাম</th>
                        <th>ধরন (Type)</th>
                        <th>সংযুক্ত প্রকল্প (Project)</th>
                        <th>দায়িত্বপ্রাপ্ত স্টোরকিপার</th>
                        <th class="text-center">লেনদেন সংখ্যা</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $wh)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $wh->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $wh->name }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $wh->location ?? 'ঢাকা' }}</small>
                        </td>
                        <td>
                            @if($wh->is_central)
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1" style="background-color: #f3e8ff; color: #7e22ce;">
                                    <i class="bi bi-diagram-3 me-1"></i> সেন্ট্রাল গোডাউন (Central)
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="bi bi-building me-1"></i> সাইট স্টোর (Site Store)
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $wh->project?->name ?? 'সমগ্র কোম্পানি (All Projects)' }}
                        </td>
                        <td>
                            @if($wh->storekeeper)
                                <div class="fw-bold">{{ $wh->storekeeper->name }}</div>
                                <small class="text-muted">{{ $wh->storekeeper->email }}</small>
                            @else
                                <span class="text-muted">বরাদ্দ নেই</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $wh->transactions_count }} টি রেকর্ড
                            </span>
                        </td>
                        <td>
                            @if($wh->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">সক্রিয়</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('warehouse.manage')
                            <a href="{{ route('warehouses.edit', $wh) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('warehouses.destroy', $wh) }}" method="POST" class="d-inline-block" onsubmit="return confirm('আপনি কি এই গোডাউন মুছে ফেলতে চান?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $wh->transactions_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-building fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো গোডাউন বা সাইট স্টোর পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($warehouses->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $warehouses->links() }}
    </div>
    @endif
</div>
@endsection
