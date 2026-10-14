@extends('layouts.app')

@section('title', 'Company Branch Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Company Branch Management</h4>
        <p class="text-muted small mb-0">Head office and regional branch offices directory (used in project dropdowns)</p>
    </div>
    @can('branch.manage')
    <a href="{{ route('branches.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-circle"></i>
        <span>Add New Branch</span>
    </a>
    @endcan
</div>

<div class="card card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('branches.index') }}" class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by branch name, code, or city..." value="{{ $search }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Search</button>
                    @if($search)
                    <a href="{{ route('branches.index') }}" class="btn btn-outline-danger">Reset</a>
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
                        <th>Branch Name</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>City & Division</th>
                        <th class="text-center">Projects</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $branch->code }}</td>
                        <td>
                            <div class="fw-bold">{{ $branch->name }}</div>
                            <small class="text-muted">{{ $branch->address ?? 'Bangladesh' }}</small>
                        </td>
                        <td>
                            @if($branch->is_head_office)
                                <span class="badge bg-primary px-2 py-1">
                                    <i class="bi bi-star-fill me-1"></i> Head Office
                                </span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    Regional Branch
                                </span>
                            @endif
                        </td>
                        <td>
                            <div><i class="bi bi-telephone me-1 text-secondary"></i> {{ $branch->phone ?? '—' }}</div>
                            <small class="text-muted"><i class="bi bi-envelope me-1"></i> {{ $branch->email ?? '—' }}</small>
                        </td>
                        <td>{{ $branch->city ?? 'Dhaka' }}, {{ $branch->division ?? 'Dhaka' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">
                                {{ $branch->projects_count }} Projects
                            </span>
                        </td>
                        <td>
                            @if($branch->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('branch.manage')
                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this branch?');">
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
                            No branches found.
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
