@extends('layouts.app')

@section('title', 'আইটেম ক্যাটাগরি ব্যবস্থাপনা (Item Categories)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">উপকরণ ক্যাটাগরি (Item Categories)</h4>
        <p class="text-muted small mb-0">রড, সিমেন্ট, বালু, পাথর ও ফিনিশিং আইটেমের ক্যাটাগরি ও গ্রুপ ব্যবস্থাপনা</p>
    </div>
    @can('item.manage')
    <a href="{{ route('item-categories.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>নতুন ক্যাটাগরি তৈরি করুন</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('item-categories.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="ক্যাটাগরির নাম বা কোড দিয়ে খুঁজুন..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> খুঁজুন</button>
                    @if($search)
                    <a href="{{ route('item-categories.index') }}" class="btn btn-outline-danger">রিসেট</a>
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
                        <th>ক্যাটাগরির নাম (English)</th>
                        <th>নাম (বাংলা)</th>
                        <th>মূল ক্যাটাগরি (Parent)</th>
                        <th class="text-center">আইটেম সংখ্যা</th>
                        <th>স্ট্যাটাস</th>
                        <th class="text-end pe-4">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $cat->code }}</td>
                        <td class="fw-bold">{{ $cat->name }}</td>
                        <td>{{ $cat->name_bn ?? '—' }}</td>
                        <td>
                            @if($cat->parent)
                                <span class="badge bg-light text-dark border">{{ $cat->parent->name }}</span>
                            @else
                                <span class="text-muted">প্রধান ক্যাটাগরি (Root)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $cat->items_count }} টি উপকরণ
                            </span>
                        </td>
                        <td>
                            @if($cat->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">সক্রিয়</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('item.manage')
                            <a href="{{ route('item-categories.edit', $cat) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('item-categories.destroy', $cat) }}" method="POST" class="d-inline-block" onsubmit="return confirm('আপনি কি এই ক্যাটাগরি মুছে ফেলতে চান?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $cat->items_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            কোনো আইটেম ক্যাটাগরি পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
