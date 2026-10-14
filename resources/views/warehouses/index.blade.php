@extends('layouts.app')

@section('title', 'Warehouses & Site Stores')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Warehouses & Site Stores</h4>
        <p class="text-muted small mb-0">Central storage yards, site stores, locations, and assigned storekeepers</p>
    </div>
    @can('warehouse.manage')
    <a href="{{ route('warehouses.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>Add New Warehouse</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('warehouses.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by warehouse name, code or location..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                    @if($search)
                    <a href="{{ route('warehouses.index') }}" class="btn btn-outline-danger">Reset</a>
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
                        <th class="ps-4">Code</th>
                        <th>Warehouse Name</th>
                        <th>Type</th>
                        <th>Assigned Project</th>
                        <th>Storekeeper</th>
                        <th class="text-center">Transactions</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $wh)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $wh->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $wh->name }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $wh->location ?? 'Dhaka' }}</small>
                        </td>
                        <td>
                            @if($wh->is_central)
                                <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1" style="background-color: #f3e8ff; color: #7e22ce;">
                                    <i class="bi bi-diagram-3 me-1"></i> Central Yard
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    <i class="bi bi-building me-1"></i> Site Store
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $wh->project?->name ?? 'All Projects (Central)' }}
                        </td>
                        <td>
                            @if($wh->storekeeper)
                                <div class="fw-bold">{{ $wh->storekeeper->name }}</div>
                                <small class="text-muted">{{ $wh->storekeeper->email }}</small>
                            @else
                                <span class="text-muted">Unassigned</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $wh->transactions_count }} records
                            </span>
                        </td>
                        <td>
                            @if($wh->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('warehouse.manage')
                            <a href="{{ route('warehouses.edit', $wh) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('warehouses.destroy', $wh) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this warehouse?');">
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
                            No warehouses or site stores found.
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
