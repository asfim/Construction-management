@extends('layouts.app')

@section('title', "Edit User — {$user->name}")

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit User Profile & Role</h4>
        <p class="text-muted small mb-0">{{ $user->name }} ({{ $user->email }})</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back to Users</span>
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
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pencil-square text-primary me-2"></i> Update User Details
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Designation / Title</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $user->designation) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">System Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>
                                    {{ $role->name }} @if(in_array($role->name, ['Project Manager', 'Project Director', 'Company Owner'])) ★ (Project Manager Dropdown) @endif
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Associated Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Select Branch (Optional)</option>
                                @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', $userBranchId) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Change Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                        </div>

                        <div class="col-12 col-md-6 d-flex align-items-center mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small" for="isActiveSwitch">Active Account</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                                <i class="bi bi-check-lg"></i>
                                <span>Update User</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
