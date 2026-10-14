@extends('layouts.app')

@section('title', __('erp.actions.edit') . ': ' . $project->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.actions.edit') }}: {{ $project->name }}</h4>
        <p class="text-muted small mb-0">Update project details, execution progress, and contract parameters</p>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
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

<form action="{{ route('projects.update', $project) }}" method="POST">
    @csrf
    @method('PUT')
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
                            <label class="form-label fw-semibold small">{{ __('erp.project.code') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $project->code }}" readonly>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">{{ __('erp.project.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.branch') }}</label>
                            <select name="branch_id" class="form-select">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $project->branch_id) == $branch->id ? 'selected' : '' }}>
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
                                <option value="{{ $manager->id }}" {{ old('project_manager_id', $project->project_manager_id) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} ({{ $manager->designation ?? 'Manager' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.description') }}</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
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
                            <input type="text" name="client_name" class="form-control" value="{{ old('client_name', $project->client_name) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_contact') }}</label>
                            <input type="text" name="client_contact_person" class="form-control" value="{{ old('client_contact_person', $project->client_contact_person) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_phone') }}</label>
                            <input type="text" name="client_phone" class="form-control" value="{{ old('client_phone', $project->client_phone) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_email') }}</label>
                            <input type="email" name="client_email" class="form-control" value="{{ old('client_email', $project->client_email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.site_address') }}</label>
                            <input type="text" name="site_address" class="form-control" value="{{ old('site_address', $project->site_address) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">City / District</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $project->city) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Division</label>
                            <input type="text" name="division" class="form-control" value="{{ old('division', $project->division) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Financial, Progress & Timeline Side Panel -->
        <div class="col-12 col-lg-4">
            <!-- Progress & Status Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-graph-up text-primary me-2"></i> Progress & Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.progress') }} (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="0" max="100" name="progress_percent" class="form-control" value="{{ old('progress_percent', $project->progress_percent) }}">
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Current Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>{{ __('erp.status.planning') }}</option>
                            <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>{{ __('erp.status.active') }}</option>
                            <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>{{ __('erp.status.on_hold') }}</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>{{ __('erp.status.completed') }}</option>
                            <option value="archived" {{ old('status', $project->status) == 'archived' ? 'selected' : '' }}>{{ __('erp.status.archived') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Financial Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-cash-coin text-warning me-2"></i> Financial Parameters (BDT)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.contract_value') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="contract_value" class="form-control" value="{{ old('contract_value', $project->contract_value) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.budget_amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="budget_amount" class="form-control" value="{{ old('budget_amount', $project->budget_amount) }}" required>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.retention') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="retention_percentage" class="form-control" value="{{ old('retention_percentage', $project->retention_percentage) }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.advance_recovery') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="advance_recovery_percentage" class="form-control" value="{{ old('advance_recovery_percentage', $project->advance_recovery_percentage) }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calendar-range text-info me-2"></i> Timeline & Schedule
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->toDateString()) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.end_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $project->end_date?->toDateString()) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Revised End Date (EOT)</label>
                        <input type="date" name="revised_end_date" class="form-control" value="{{ old('revised_end_date', $project->revised_end_date?->toDateString()) }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> {{ __('erp.actions.update') }}
            </button>
        </div>
    </div>
</form>
@endsection
