@extends('layouts.app')

@section('title', $defaultRole === 'Project Manager' ? 'Add Project Manager' : 'Add New User')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            {{ $defaultRole === 'Project Manager' ? 'Add Project Manager' : 'Add New User / Staff' }}
        </h4>
        <p class="text-muted small mb-0">Create staff profile and assign role (Project Manager, Director, Engineer, etc.)</p>
    </div>
    <a href="{{ route('users.index', $defaultRole ? ['role' => $defaultRole] : []) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back to List</span>
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please correct the following errors:</strong>
    <ul class="mb-0 small ps-3">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card card-custom border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-plus text-primary me-2"></i> User & Role Credentials
                </h6>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                    Appears in Project Manager Dropdown
                </span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Engr. Tanvir Ahmed" value="{{ old('name') }}" required>
                        </div>

                        <!-- Email -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="tanvir@example.com" value="{{ old('email') }}" required>
                        </div>

                        <!-- Phone -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+88017XXXXXXXX" value="{{ old('phone') }}">
                        </div>

                        <!-- Designation -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Designation / Title</label>
                            <input type="text" name="designation" class="form-control" placeholder="e.g. Senior Project Manager" value="{{ old('designation', $defaultRole) }}">
                        </div>

                        <!-- Role -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $defaultRole) === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }} @if(in_array($role->name, ['Project Manager', 'Project Director', 'Company Owner'])) ★ (Project Manager Dropdown) @endif
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                Selecting <strong>Project Manager</strong>, <strong>Project Director</strong>, or <strong>Company Owner</strong> allows them to be assigned to projects.
                            </small>
                        </div>

                        <!-- Associated Branch -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Associated Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Select Branch (Optional)</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Initial Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Defaults to: password123">
                            <small class="text-muted">Leave blank for default password (<code>password123</code>)</small>
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small" for="isActiveSwitch">Active Account</label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="col-12 mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                <span>Save & Create User</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
