@extends('layouts.app')

@section('title', 'Role Details: ' . $role->name)

@section('content')
@php
    $isSystemRole = in_array($role->name, $systemRoles, true);
@endphp

<!-- Header -->
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark">{{ $role->name }}</h4>
            @if($isSystemRole)
            <span class="badge bg-secondary-subtle text-secondary border">Core System Role</span>
            @else
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Custom Role</span>
            @endif
        </div>
        <p class="text-muted small mb-0">Role privileges, granted permissions matrix, and assigned staff members</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @can('role.manage')
        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-pencil-square"></i>
            <span>Configure Permissions</span>
        </a>
        @endcan
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('erp.actions.back') }}</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Role Overview & Assigned Staff -->
    <div class="col-12 col-xl-4">
        <!-- Overview Card -->
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-info-circle text-primary me-2"></i> Role Summary
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center py-2 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow mx-auto mb-2" 
                         style="width: 56px; height: 56px; font-size: 1.5rem; background: {{ $role->name === 'Super Admin' ? 'linear-gradient(135deg, #1e3a8a, #7c3aed)' : 'linear-gradient(135deg, #0284c7, #0d9488)' }};">
                        <i class="bi {{ $role->name === 'Super Admin' ? 'bi-shield-shaded' : 'bi-person-badge' }}"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-0">{{ $role->name }}</h5>
                    <small class="text-muted">guard: {{ $role->guard_name }}</small>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Total Granted Permissions:</span>
                        <strong class="text-primary">{{ $role->permissions_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Assigned Staff Count:</span>
                        <strong class="text-dark">{{ $role->users_count }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Role Type:</span>
                        <span>{{ $isSystemRole ? 'Core Protected Role' : 'Custom Defined Role' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Assigned Staff Members -->
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-people text-primary me-2"></i> Assigned Staff ({{ $role->users->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($role->users as $user)
                    <div class="list-group-item p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong class="text-dark d-block small">{{ $user->name }}</strong>
                                <small class="text-muted">{{ $user->email }}</small>
                                @if($user->designation)
                                <div class="text-primary small" style="font-size: 0.72rem;">{{ $user->designation }}</div>
                                @endif
                            </div>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Edit User">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted small">
                        No active users currently assigned to this role.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Granted Permissions by Module -->
    <div class="col-12 col-xl-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-key text-primary me-2"></i> Granted Permissions By Module
                    </h6>
                    <small class="text-muted">All active privileges granted to this role</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    {{ $role->permissions_count }} Active
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    @foreach($permissionGroups as $groupName => $group)
                    @php
                        $groupKeys = array_keys($group['permissions']);
                        $grantedInGroup = array_intersect($groupKeys, $rolePermissions);
                    @endphp
                    <div class="col-12">
                        <div class="border rounded-3 p-3 {{ count($grantedInGroup) > 0 ? 'bg-light-subtle' : 'opacity-75' }}">
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $group['icon'] }} text-primary fs-5"></i>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark small">{{ $groupName }}</h6>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $group['description'] }}</span>
                                    </div>
                                </div>
                                <span class="badge {{ count($grantedInGroup) > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }}" style="font-size: 0.72rem;">
                                    {{ count($grantedInGroup) }} / {{ count($groupKeys) }} Granted
                                </span>
                            </div>
                            <div class="row g-2 pt-1">
                                @foreach($group['permissions'] as $permName => $permLabel)
                                @php
                                    $isGranted = in_array($permName, $rolePermissions, true);
                                @endphp
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center gap-2 p-1.5 rounded {{ $isGranted ? 'text-dark' : 'text-muted' }}">
                                        @if($isGranted)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-dash-circle text-secondary opacity-50"></i>
                                        @endif
                                        <span class="small {{ $isGranted ? 'fw-medium' : 'text-decoration-line-through opacity-75' }}">{{ $permLabel }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
