@extends('layouts.app')

@section('title', 'Edit Role: ' . $role->name)

@section('content')
@php
    $isSystemRole = in_array($role->name, $systemRoles, true);
@endphp

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark">Configure Role: {{ $role->name }}</h4>
            @if($isSystemRole)
            <span class="badge bg-secondary-subtle text-secondary border">Core System Role</span>
            @else
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Custom Role</span>
            @endif
        </div>
        <p class="text-muted small mb-0">Configure granted permissions and operational privileges for all users with this role</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary d-flex align-items-center gap-1">
            <i class="bi bi-eye"></i>
            <span>Role Details</span>
        </a>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('erp.actions.back') }}</span>
        </a>
    </div>
</div>

@if($role->name === 'Super Admin')
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4">
    <i class="bi bi-shield-check fs-3 text-primary"></i>
    <div>
        <strong class="d-block">Super Admin Has Universal Access</strong>
        <span class="small">The Super Admin role is programmed with full access to all existing and future ERP modules by default.</span>
    </div>
</div>
@endif

<form action="{{ route('roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Role Basic Information -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-person-badge text-primary me-2"></i> Role Profile
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Role Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $role->name) }}" 
                           {{ $isSystemRole ? 'readonly' : 'required' }}>
                    @if($isSystemRole)
                        <div class="form-text text-muted small"><i class="bi bi-lock me-1"></i> Core system role names are protected and cannot be altered.</div>
                    @endif
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Guard &amp; Users</label>
                    <div class="form-control bg-light d-flex align-items-center justify-content-between">
                        <span class="font-monospace text-muted small">guard: {{ $role->guard_name }}</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                            {{ $role->users()->count() }} Staff Member(s) Assigned
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Matrix -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-3 gap-2">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-shield-check text-primary me-2"></i> Module Permissions Matrix
            </h5>
            <small class="text-muted">
                Currently Selected: <strong id="selectedPermCount" class="text-primary">{{ count($rolePermissions) }}</strong> permissions
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary shadow-xs" onclick="toggleAllPermissions(true)">
                <i class="bi bi-check-all me-1"></i> Select All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary shadow-xs" onclick="toggleAllPermissions(false)">
                <i class="bi bi-x-circle me-1"></i> Clear All
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($permissionGroups as $groupName => $group)
        @php
            $groupKeys = array_keys($group['permissions']);
            $groupAssignedCount = count(array_intersect($groupKeys, $rolePermissions));
        @endphp
        <div class="col-12 col-lg-6">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="bi {{ $group['icon'] }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small">{{ $groupName }}</h6>
                            <span class="text-muted" style="font-size: 0.7rem;">
                                <span class="badge bg-light text-dark border font-monospace">{{ $groupAssignedCount }} / {{ count($groupKeys) }}</span>
                                {{ $group['description'] }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-xs btn-link text-decoration-none p-0 text-primary small" onclick="toggleGroupPermissions('{{ Str::slug($groupName) }}', true)">
                            All
                        </button>
                        <span class="text-muted mx-1">/</span>
                        <button type="button" class="btn btn-xs btn-link text-decoration-none p-0 text-muted small" onclick="toggleGroupPermissions('{{ Str::slug($groupName) }}', false)">
                            None
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        @foreach($group['permissions'] as $permName => $permLabel)
                        @php
                            $isChecked = in_array($permName, old('permissions', $rolePermissions));
                        @endphp
                        <div class="col-12">
                            <div class="form-check form-switch p-2 rounded hover-bg-subtle d-flex align-items-center justify-content-between">
                                <label class="form-check-label small fw-medium text-dark flex-grow-1 cursor-pointer" for="perm_{{ Str::slug($permName) }}">
                                    <span>{{ $permLabel }}</span>
                                    <span class="badge bg-light text-muted font-monospace border ms-1" style="font-size: 0.65rem;">{{ $permName }}</span>
                                </label>
                                <input class="form-check-input perm-check group-{{ Str::slug($groupName) }}" 
                                       type="checkbox" 
                                       role="switch"
                                       name="permissions[]" 
                                       value="{{ $permName }}" 
                                       id="perm_{{ Str::slug($permName) }}"
                                       onchange="updatePermCount()"
                                       {{ $isChecked ? 'checked' : '' }}>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-2 mb-5">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4 py-2">
            Cancel
        </a>
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-check2-circle me-1"></i> Update Role Permissions
        </button>
    </div>
</form>

<style>
.cursor-pointer { cursor: pointer; }
.hover-bg-subtle:hover { background-color: #f8fafc; }
.btn-xs { font-size: 0.72rem; }
</style>

<script>
function updatePermCount() {
    const checked = document.querySelectorAll('.perm-check:checked').length;
    const el = document.getElementById('selectedPermCount');
    if (el) el.textContent = checked;
}
function toggleAllPermissions(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
    updatePermCount();
}
function toggleGroupPermissions(groupClass, checked) {
    document.querySelectorAll('.group-' + groupClass).forEach(cb => cb.checked = checked);
    updatePermCount();
}
</script>
@endsection
