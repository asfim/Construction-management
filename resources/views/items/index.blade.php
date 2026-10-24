@extends('layouts.app')

@section('title', 'Item Master')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Material Master (Item Catalog)</h4>
        <p class="text-muted small mb-0">Construction materials, technical specifications, standard cost rates, and units</p>
    </div>
    @can('item.manage')
    <a href="{{ route('items.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>Add New Material</span>
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
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by code, name or brand..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="category_id" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
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
                        <th>Code</th>
                        <th>Material Name & Specification</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Standard Rate</th>
                        <th>Min Stock Alert</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Actions</th>
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
                            <small class="text-muted">
                                @if($item->brand) <strong>Brand:</strong> {{ $item->brand }} | @endif
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
                        </td>
                        <td class="fw-bold text-dark">
                            ৳ {{ number_format($item->standard_rate, 2) }}
                        </td>
                        <td>
                            <span class="text-muted font-monospace">{{ number_format($item->min_stock_alert, 2) }}</span> {{ $item->unit?->symbol }}
                        </td>
                        <td>
                            @if($item->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                @can('item.manage')
                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit Material">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                                <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete material \'{{ addslashes($item->name) }}\'?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Material">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No materials found.
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
