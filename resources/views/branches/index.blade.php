@extends('layouts.app')

@section('title', 'কোম্পানির শাখা ব্যবস্থাপনা (Branches)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">কোম্পানির শাখা ব্যবস্থাপনা (Branch Management)</h4>
        <p class="text-muted small mb-0">হেড অফিস ও আঞ্চলিক শাখার তালিকা (নতুন প্রজেক্ট খোলার সময় ড্রপডাউনে ব্যবহৃত)</p>
    </div>
    @can('branch.manage')
    <a href="{{ route('branches.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>নতুন শাখা যোগ করুন</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('branches.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="শাখার নাম, কোড বা শহর দিয়ে খুঁজুন..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> খুঁজুন</button>
                    @if($search)
                    <a href="{{ route('branches.index') }}" class="btn btn-outline-danger">রিসেট</a>
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
                        <th>শাখার নাম</th>
                        <th>ধরন</th>
                        <th>যোগাযোগ</th>
                        <th>শহর ও বিভাগ</th>
                        <th class="text-center">প্রকল্প সংখ্যা</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $branch->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $branch->name }}</div>
                            <small class="text-muted">{{ $branch->address ?? 'বাংলাদেশ' }}</small>
                        </td>
                        <td>
                            @if($branch->is_head_office)
                                <span class="badge bg-primary px-2 py-1">
                                    <i class="bi bi-star-fill me-1"></i> হেড অফিস (Head Office)
                                </span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    আঞ্চলিক শাখা
                                </span>
                            @endif
                        </td>
                        <td>
                            <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $branch->phone ?? '—' }}</div>
                            <small class="text-muted"><i class="bi bi-envelope me-1"></i> {{ $branch->email ?? '—' }}</small>
                        </td>
                        <td>{{ $branch->city ?? 'ঢাকা' }}, {{ $branch->division ?? 'ঢাকা' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $branch->projects_count }} টি প্রকল্প
                            </span>
                        </td>
                        <td>
                            @if($branch->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">সক্রিয়</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('branch.manage')
                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline-block" onsubmit="return confirm('আপনি কি এই শাখা মুছে ফেলতে চান?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $branch->projects_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো শাখা পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($branches->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $branches->links() }}
    </div>
    @endif
</div>
@endsection
