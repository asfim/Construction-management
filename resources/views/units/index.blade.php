@extends('layouts.app')

@section('title', 'Units of Measurement')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Units of Measurement (UoM)</h4>
        <p class="text-muted small mb-0">Measurement units used for construction materials and BOQ estimation (Bag, Ton, CFT, SFT, etc.)</p>
    </div>
    @can('item.manage')
    <a href="{{ route('units.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>Add New Unit</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('units.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by unit name or symbol..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                    @if($search)
                    <a href="{{ route('units.index') }}" class="btn btn-outline-danger">Reset</a>
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
                        <th class="ps-4">Unit Name</th>
                        <th>Symbol</th>
                        <th>Measurement Type</th>
                        <th class="text-center">Assigned Items</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $unit->name }}</td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6">{{ $unit->symbol }}</span></td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ ucfirst($unit->type) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $unit->items_count }} items
                            </span>
                        </td>
                        <td>
                            @if($unit->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('item.manage')
                            <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this unit?');">
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-rulers fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No units of measurement found.
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
