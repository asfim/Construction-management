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
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold small mb-0">{{ __('erp.project.branch') }}</label>
                                @can('branch.manage')
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#quickAddBranchModal">
                                    <i class="bi bi-plus-circle me-1"></i> + New Branch
                                </button>
                                @endcan
                            </div>
                            <select name="branch_id" id="project_branch_id" class="form-select">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $project->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold small mb-0">{{ __('erp.project.manager') }}</label>
                                @can('user.manage')
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small text-primary" data-bs-toggle="modal" data-bs-target="#quickAddManagerModal">
                                    <i class="bi bi-person-plus me-1"></i> + New Manager
                                </button>
                                @endcan
                            </div>
                            <select name="project_manager_id" id="project_manager_id" class="form-select">
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

<!-- Modal: Quick Add Branch -->
<div class="modal fade" id="quickAddBranchModal" tabindex="-1" aria-labelledby="quickAddBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="quickAddBranchModalLabel">
                    <i class="bi bi-diagram-3 text-primary me-2"></i> Add New Associated Branch
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddBranchForm">
                @csrf
                <div class="modal-body p-4">
                    <div id="quickBranchAlert" class="alert alert-danger d-none py-2 small"></div>
                    <div class="row g-3">
                        <div class="col-12 col-md-5">
                            <label class="form-label fw-semibold small">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="BRN-DHK-01" required value="BRN-{{ strtoupper(Str::random(4)) }}">
                        </div>
                        <div class="col-12 col-md-7">
                            <label class="form-label fw-semibold small">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Dhaka Corporate Branch" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" class="form-control" placeholder="e.g. Dhaka">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Division</label>
                            <input type="text" name="division" class="form-control" placeholder="e.g. Dhaka">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Phone / Contact</label>
                            <input type="text" name="phone" class="form-control" placeholder="+8801XXXXXXXXX">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_head_office" id="branchHeadOffice" value="1">
                                <label class="form-check-label small" for="branchHeadOffice">Head Office</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="saveBranchBtn">
                        <i class="bi bi-check-lg me-1"></i> Save & Select Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Quick Add Project Manager -->
<div class="modal fade" id="quickAddManagerModal" tabindex="-1" aria-labelledby="quickAddManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="quickAddManagerModalLabel">
                    <i class="bi bi-person-plus text-primary me-2"></i> Add New Project Manager
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddManagerForm">
                @csrf
                <input type="hidden" name="role" value="Project Manager">
                <div class="modal-body p-4">
                    <div id="quickManagerAlert" class="alert alert-danger d-none py-2 small"></div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Engr. Shafiqul Islam" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="shafiqul@company.com" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Phone Number</label>
                            <input type="text" name="phone" class="form-control" placeholder="+88017XXXXXXXX">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Designation / Title</label>
                            <input type="text" name="designation" class="form-control" placeholder="Project Manager" value="Project Manager">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Initial Password (Optional)</label>
                            <input type="password" name="password" class="form-control" placeholder="Default: password123">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="saveManagerBtn">
                        <i class="bi bi-check-lg me-1"></i> Save & Select Manager
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Quick Add Branch AJAX
    const branchForm = document.getElementById('quickAddBranchForm');
    if (branchForm) {
        branchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('saveBranchBtn');
            const alertBox = document.getElementById('quickBranchAlert');
            alertBox.classList.add('d-none');
            alertBox.textContent = '';
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const formData = new FormData(branchForm);

            fetch("{{ route('branches.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(result => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Branch';

                if (!result.ok) {
                    const errors = result.data.errors ? Object.values(result.data.errors).flat().join('<br>') : (result.data.message || 'Error creating branch.');
                    alertBox.innerHTML = errors;
                    alertBox.classList.remove('d-none');
                    return;
                }

                const select = document.getElementById('project_branch_id');
                const opt = document.createElement('option');
                opt.value = result.data.branch.id;
                opt.textContent = result.data.branch.name;
                opt.selected = true;
                select.appendChild(opt);

                bootstrap.Modal.getInstance(document.getElementById('quickAddBranchModal')).hide();
                branchForm.reset();
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Branch';
                alertBox.textContent = 'Server error. Please try again.';
                alertBox.classList.remove('d-none');
            });
        });
    }

    // Quick Add Project Manager AJAX
    const managerForm = document.getElementById('quickAddManagerForm');
    if (managerForm) {
        managerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('saveManagerBtn');
            const alertBox = document.getElementById('quickManagerAlert');
            alertBox.classList.add('d-none');
            alertBox.textContent = '';
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

            const formData = new FormData(managerForm);

            fetch("{{ route('users.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, data })))
            .then(result => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Manager';

                if (!result.ok) {
                    const errors = result.data.errors ? Object.values(result.data.errors).flat().join('<br>') : (result.data.message || 'Error creating project manager.');
                    alertBox.innerHTML = errors;
                    alertBox.classList.remove('d-none');
                    return;
                }

                const select = document.getElementById('project_manager_id');
                const opt = document.createElement('option');
                opt.value = result.data.user.id;
                opt.textContent = `${result.data.user.name} (${result.data.user.designation || 'Project Manager'})`;
                opt.selected = true;
                select.appendChild(opt);

                bootstrap.Modal.getInstance(document.getElementById('quickAddManagerModal')).hide();
                managerForm.reset();
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Save & Select Manager';
                alertBox.textContent = 'Server error. Please try again.';
                alertBox.classList.remove('d-none');
            });
        });
    }
});
</script>
@endpush
@endsection
