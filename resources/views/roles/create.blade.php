@extends('layouts.app')

@section('title', 'Create New Role')

@section('content')
<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Create New Role</h4>
        <p class="text-muted small mb-0">Define a custom organizational role and assign granular module permissions</p>
    </div>
    <div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('erp.actions.back') }}</span>
        </a>
    </div>
</div>

<form action="{{ route('roles.store') }}" method="POST">
    @csrf

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
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Lead Structural Engineer, Assistant Storekeeper" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Description / Operational Scope</label>
                    <input type="text" name="description" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" placeholder="Brief description of responsibilities">
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
            <small class="text-muted">Select the specific privileges granted to this role across ERP modules</small>
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
        <div class="col-12 col-lg-6">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="bi {{ $group['icon'] }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small">{{ $groupName }}</h6>
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $group['description'] }}</span>
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
                                       {{ in_array($permName, old('permissions', [])) ? 'checked' : '' }}>
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
            <i class="bi bi-check2-circle me-1"></i> Save &amp; Create Role
        </button>
    </div>
</form>

<style>
.cursor-pointer { cursor: pointer; }
.hover-bg-subtle:hover { background-color: #f8fafc; }
.btn-xs { font-size: 0.72rem; }
</style>

<script>
function toggleAllPermissions(checked) {
    document.querySelectorAll('.perm-check').forEach(cb => cb.checked = checked);
}
function toggleGroupPermissions(groupClass, checked) {
    document.querySelectorAll('.group-' + groupClass).forEach(cb => cb.checked = checked);
}
</script>
@endsection
