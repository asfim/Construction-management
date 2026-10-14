@extends('layouts.app')

@section('title', __('erp.nav.new_project'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.new_project') }}</h4>
        <p class="text-muted small mb-0">Register new construction project profile, contract details, and parameters</p>
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
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

<form action="{{ route('projects.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- 1. General Project Details -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i> 1. General Project Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">{{ __('erp.project.code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="PRJ-DHK-001" value="{{ old('code', 'PRJ-' . strtoupper(Str::random(6))) }}" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">{{ __('erp.project.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter full project title" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.branch') }}</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Select Branch (Optional)</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.manager') }}</label>
                            <select name="project_manager_id" class="form-select">
                                <option value="">Select Project Manager</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} ({{ $manager->designation ?? 'Manager' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Scope of work and detailed description">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Client & Site Location -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-geo-alt text-success me-2"></i> 2. Client & Site Location
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client') }}</label>
                            <input type="text" name="client_name" class="form-control" placeholder="Client or Employer Organization" value="{{ old('client_name') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_contact') }}</label>
                            <input type="text" name="client_contact_person" class="form-control" placeholder="Representative / In-charge Name" value="{{ old('client_contact_person') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_phone') }}</label>
                            <input type="text" name="client_phone" class="form-control" placeholder="+8801XXXXXXXXX" value="{{ old('client_phone') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_email') }}</label>
                            <input type="email" name="client_email" class="form-control" placeholder="client@example.com" value="{{ old('client_email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.site_address') }}</label>
                            <input type="text" name="site_address" class="form-control" placeholder="Plot, Road, Area..." value="{{ old('site_address') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">City / District</label>
                            <input type="text" name="city" class="form-control" placeholder="Dhaka, Chattogram, etc." value="{{ old('city') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Division</label>
                            <select name="division" class="form-select">
                                <option value="">Select Division</option>
                                <option value="Dhaka" {{ old('division') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chattogram" {{ old('division') == 'Chattogram' ? 'selected' : '' }}>Chattogram</option>
                                <option value="Rajshahi" {{ old('division') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ old('division') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Barishal" {{ old('division') == 'Barishal' ? 'selected' : '' }}>Barishal</option>
                                <option value="Sylhet" {{ old('division') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Rangpur" {{ old('division') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ old('division') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Financial, Contract & Timeline Side Panel -->
        <div class="col-12 col-lg-4">
            <!-- Financial Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-cash-coin text-warning me-2"></i> 3. Financial Parameters
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.contract_value') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="contract_value" class="form-control" placeholder="0.00" value="{{ old('contract_value', '0.00') }}" required>
                        </div>
                        <small class="text-muted">Total Contract Value (BDT)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.budget_amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="budget_amount" class="form-control" placeholder="0.00" value="{{ old('budget_amount', '0.00') }}" required>
                        </div>
                        <small class="text-muted">Approved internal cost ceiling</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.retention') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="retention_percentage" class="form-control" value="{{ old('retention_percentage', '5.0') }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.advance_recovery') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="advance_recovery_percentage" class="form-control" value="{{ old('advance_recovery_percentage', '10.0') }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline & Status Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calendar-range text-info me-2"></i> 4. Timeline & Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.end_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Initial Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>{{ __('erp.status.planning') }}</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('erp.status.active') }}</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>{{ __('erp.status.on_hold') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submission Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> {{ __('erp.actions.save') }}
            </button>
        </div>
    </div>
</form>
@endsection
