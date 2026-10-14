@extends('layouts.app')

@section('title', 'পরিমাপের একক ব্যবস্থাপনা (Units of Measurement)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">পরিমাপের একক (Units of Measurement)</h4>
        <p class="text-muted small mb-0">কনস্ট্রাকশন মালামাল ও বিওকিউ-এর জন্য ব্যবহৃত সকল পরিমাপের একক (Bag, Ton, CFT, SFT ইত্যাদি)</p>
    </div>
    @can('item.manage')
    <a href="{{ route('units.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>নতুন একক তৈরি করুন</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('units.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="এককের নাম বা প্রতীক দিয়ে খুঁজুন..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> খুঁজুন</button>
                    @if($search)
                    <a href="{{ route('units.index') }}" class="btn btn-outline-danger">রিসেট</a>
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
                        <th class="ps-4">এককের নাম (English)</th>
                        <th>নাম (বাংলা)</th>
                        <th>প্রতীক (Symbol)</th>
                        <th>পরিমাপের ধরন (Type)</th>
                        <th class="text-center">ব্যবহৃত আইটেম</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $unit->name }}</td>
                        <td>{{ $unit->name_bn ?? '—' }}</td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">{{ $unit->symbol }}</span></td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ ucfirst($unit->type) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $unit->items_count }} টি উপকরণ
                            </span>
                        </td>
                        <td>
                            @if($unit->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">সক্রিয়</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('item.manage')
                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline-block" onsubmit="return confirm('আপনি কি এই একক মুছে ফেলতে চান?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $unit->items_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-rulers fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো পরিমাপের একক পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($units->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $units->links() }}
    </div>
    @endif
</div>
@endsection
