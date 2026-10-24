@extends('layouts.app')

@section('title', 'Users & Project Managers')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-people-fill text-primary me-2"></i> Users & Project Managers
        </h4>
        <p class="text-muted small mb-0">Manage company staff, project managers, directors, and system access</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('role.view')
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-shield-check"></i>
            <span>Roles &amp; Permissions</span>
        </a>
        @endcan
        @can('user.manage')
        <a href="{{ route('users.create', $roleFilter ? ['role' => $roleFilter] : []) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-person-plus-fill"></i>
            <span>{{ $roleFilter ? "Add {$roleFilter}" : 'Add New User / Staff' }}</span>
        </a>
        @endcan
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Total Users</span>
            <h4 class="fw-bold text-dark mb-0">{{ $users->total() }}</h4>
            <small class="text-muted">Registered company members</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Project Managers / Directors</span>
            <h4 class="fw-bold text-warning-emphasis mb-0">{{ $totalManagers }}</h4>
            <small class="text-muted">Eligible in project manager dropdowns</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Quick Action</span>
            <a href="{{ route('projects.create') }}" class="btn btn-sm btn-outline-primary mt-1">
                <i class="bi bi-plus-circle me-1"></i> Go to New Project Form
            </a>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search by name, email, phone, designation..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ $roleFilter === $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                @if($search || $roleFilter)
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Designation</th>
                        <th>Role / Permission</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                     style="width: 38px; height: 38px; background: linear-gradient(135deg, #4f46e5, #0ea5e9); font-size: 0.85rem;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark fw-semibold small">{{ $user->designation ?? '—' }}</span>
                        </td>
                        <td>
                            @forelse($user->roles as $r)
                                @if(in_array($r->name, ['Project Manager', 'Project Director', 'Company Owner']))
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle font-monospace">
                                        <i class="bi bi-star-fill me-1"></i> {{ $r->name }}
                                    </span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">
                                        {{ $r->name }}
                                    </span>
                                @endif
                            @empty
                                <span class="badge bg-secondary-subtle text-muted">No Role</span>
                            @endforelse
                        </td>
                        <td>
                            <div class="small">
                                @if($user->phone)
                                <div class="text-dark"><i class="bi bi-telephone me-1 text-muted"></i> {{ $user->phone }}</div>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($user->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            @can('user.manage')
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.edit', $user) }}">
                                            <i class="bi bi-pencil me-2 text-primary"></i> Edit Profile & Role
                                        </a>
                                    </li>
                                    @if($user->id !== auth()->id())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash me-2"></i> Delete User
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary"></i>
                            No users found matching criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
