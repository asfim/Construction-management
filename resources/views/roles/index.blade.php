@extends('layouts.app')

@section('title', 'Roles & Permissions Management')

@section('content')
<!-- Page Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark">Roles &amp; Permissions</h4>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                <i class="bi bi-shield-check me-1"></i> Access Control Matrix
            </span>
        </div>
        <p class="text-muted small mb-0">Role-based access control (RBAC), security privileges, and module permission assignments</p>
    </div>
    @can('role.manage')
    <a href="{{ route('roles.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>Create New Role</span>
    </a>
    @endcan
</div>

<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom border-0 shadow-sm rounded-3 bg-white">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Defined Roles</span>
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalRoles }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">System &amp; custom roles</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom border-0 shadow-sm rounded-3 bg-white">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total System Permissions</span>
                    <div class="rounded-circle bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-key-fill"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-info-emphasis">{{ $totalPermissions }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Granular action permissions</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom border-0 shadow-sm rounded-3 bg-white">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Assigned Staff</span>
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success">{{ $totalAssignedUsers }}</h3>
                <small class="text-muted" style="font-size: 0.75rem;">Active users with roles</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom border-0 shadow-sm rounded-3 bg-white">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">ERP Security Mode</span>
                    <div class="rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-0 text-dark mt-1">Strict RBAC</h5>
                <small class="text-muted" style="font-size: 0.75rem;">Multi-tenant isolated</small>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('roles.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8 col-lg-9">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search role name..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                @if(!empty($search))
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary" title="Reset Search">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Roles Table Card -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-3" style="width: 250px;">Role Name</th>
                        <th style="width: 200px;">Assigned Permissions</th>
                        <th style="width: 150px;">Assigned Staff</th>
                        <th>Classification</th>
                        <th class="text-end pe-3" style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    @php
                        $isSystem = in_array($role->name, $systemRoles, true);
                        $percent = $totalPermissions > 0 ? round(($role->permissions_count / $totalPermissions) * 100) : 0;
                        $progressColor = match(true) {
                            $percent >= 80 => 'bg-danger',
                            $percent >= 40 => 'bg-primary',
                            $percent >= 20 => 'bg-info',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" 
                                     style="width: 36px; height: 36px; font-size: 0.85rem; background: {{ $role->name === 'Super Admin' ? 'linear-gradient(135deg, #1e3a8a, #7c3aed)' : 'linear-gradient(135deg, #0284c7, #0d9488)' }};">
                                    <i class="bi {{ $role->name === 'Super Admin' ? 'bi-shield-shaded' : 'bi-person-badge' }}"></i>
                                </div>
                                <div>
                                    <a href="{{ route('roles.show', $role) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $role->name }}
                                    </a>
                                    <div class="d-flex align-items-center gap-1 mt-0.5">
                                        @if($isSystem)
                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.68rem;">Core Role</span>
                                        @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.68rem;">Custom Role</span>
                                        @endif
                                        @if($role->name === 'Super Admin')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;">Full Access</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center justify-content-between mb-1" style="max-width: 180px;">
                                <span class="fw-bold small text-dark">{{ $role->permissions_count }}</span>
                                <span class="text-muted small" style="font-size: 0.75rem;">of {{ $totalPermissions }}</span>
                            </div>
                            <div class="progress" style="height: 6px; max-width: 180px;">
                                <div class="progress-bar {{ $progressColor }}" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">
                                    <i class="bi bi-people me-1 text-primary"></i> {{ $role->users_count }}
                                </span>
                                @if($role->users_count > 0)
                                <small class="text-muted">Users</small>
                                @endif
                            </div>
                        </td>

                        <td>
                            <small class="text-secondary d-block">
                                @if($role->name === 'Super Admin' || $role->name === 'Company Owner' || $role->name === 'Managing Director')
                                    <i class="bi bi-building text-primary me-1"></i> Executive / Board Level
                                @elseif(str_contains($role->name, 'Project') || str_contains($role->name, 'Site') || str_contains($role->name, 'Planning'))
                                    <i class="bi bi-kanban text-success me-1"></i> Site &amp; Project Engineering
                                @elseif(str_contains($role->name, 'Procurement') || str_contains($role->name, 'Storekeeper'))
                                    <i class="bi bi-boxes text-info me-1"></i> Supply Chain &amp; Warehousing
                                @elseif(str_contains($role->name, 'Accounts') || str_contains($role->name, 'Accountant'))
                                    <i class="bi bi-cash-coin text-warning me-1"></i> Commercial &amp; Finance
                                @elseif(str_contains($role->name, 'Portal'))
                                    <i class="bi bi-globe text-secondary me-1"></i> External Portal Stakeholder
                                @else
                                    <i class="bi bi-shield text-muted me-1"></i> Standard Operations
                                @endif
                            </small>
                        </td>

                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary" title="View Role Details & Permissions">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @can('role.manage')
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary" title="Configure Permissions">
                                    <i class="bi bi-pencil-square"></i>
                                    <span class="d-none d-md-inline ms-1">Configure</span>
                                </a>
                                @if(! $isSystem && $role->users_count === 0)
                                <button type="button" class="btn btn-outline-danger" title="Delete Custom Role"
                                        onclick="if(confirm('Are you sure you want to delete role \'{{ $role->name }}\'?')) { document.getElementById('delete-role-{{ $role->id }}').submit(); }">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-role-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x fs-1 d-block mb-2 text-muted"></i>
                            No roles found matching your search.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($roles->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
