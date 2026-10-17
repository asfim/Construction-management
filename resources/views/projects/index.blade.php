@extends('layouts.app')

@section('title', __('erp.nav.project_list'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.project_list') }}</h4>
        <p class="text-muted small mb-0">All construction projects and active site supervision list</p>
    </div>
    @can('project.create')
    <a href="{{ route('projects.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>{{ __('erp.dashboard.create_project') }}</span>
    </a>
    @endcan
</div>

<!-- Search & Filters -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('projects.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('erp.actions.search') }}" value="{{ $search }}">
                </div>
            </div>
            <div class="col-6 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="planning" {{ $status == 'planning' ? 'selected' : '' }}>{{ __('erp.status.planning') }}</option>
                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>{{ __('erp.status.active') }}</option>
                    <option value="on_hold" {{ $status == 'on_hold' ? 'selected' : '' }}>{{ __('erp.status.on_hold') }}</option>
                    <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>{{ __('erp.status.completed') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> {{ __('erp.actions.filter') }}
                </button>
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Projects Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>{{ __('erp.project.code') }}</th>
                        <th>{{ __('erp.project.name') }}</th>
                        <th>{{ __('erp.project.client') }}</th>
                        <th>{{ __('erp.project.contract_value') }}</th>
                        <th>{{ __('erp.project.budget_amount') }}</th>
                        <th>{{ __('erp.project.progress') }}</th>
                        <th>Status</th>
                        <th class="text-end">{{ __('erp.actions.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $project->name }}
                            </a>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i> {{ $project->city ?? 'Bangladesh' }}
                                @if($project->branch)
                                | <i class="bi bi-building me-1"></i> {{ $project->branch->name }}
                                @endif
                            </small>
                        </td>
                        <td>
                            <span class="text-dark small fw-medium">{{ $project->client_name ?? '—' }}</span>
                        </td>
                        <td class="fw-semibold text-dark">
                            ৳ {{ number_format($project->contract_value, 2) }}
                        </td>
                        <td class="fw-semibold text-muted">
                            ৳ {{ number_format($project->budget_amount, 2) }}
                        </td>
                        <td style="min-width: 130px;">
                            @can('project.edit')
                            <button type="button" class="btn btn-link p-0 text-decoration-none w-100 text-start" data-bs-toggle="modal" data-bs-target="#updateProgressModal{{ $project->id }}" title="Click to update progress">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 7px; cursor: pointer;">
                                        <div class="progress-bar bg-primary" id="tableProgressBar{{ $project->id }}" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                                    </div>
                                    <small class="text-primary fw-bold text-nowrap" id="tableProgressText{{ $project->id }}">{{ number_format($project->progress_percent, 1) }}% <i class="bi bi-pencil" style="font-size: 0.65rem;"></i></small>
                                </div>
                            </button>
                            @else
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                                </div>
                                <small class="text-muted fw-semibold">{{ number_format($project->progress_percent, 1) }}%</small>
                            </div>
                            @endcan
                        </td>
                        <td>
                            <span class="badge badge-status badge-{{ $project->status }}" id="tableStatusBadge{{ $project->id }}">
                                {{ __('erp.status.' . $project->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projects.show', $project) }}">
                                            <i class="bi bi-eye text-primary me-2"></i> {{ __('erp.actions.view') }}
                                        </a>
                                    </li>
                                    @can('project.edit')
                                    <li>
                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#updateProgressModal{{ $project->id }}">
                                            <i class="bi bi-speedometer2 text-info me-2"></i> Update Progress (%)
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                            <i class="bi bi-pencil-square text-warning me-2"></i> {{ __('erp.actions.edit') }}
                                        </a>
                                    </li>
                                    @endcan
                                    @can('project.delete')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('{{ __('erp.actions.confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash text-danger me-2"></i> {{ __('erp.actions.delete') }}
                                            </button>
                                        </form>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                            {{ __('erp.dashboard.no_projects_found') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($projects->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $projects->links() }}
    </div>
    @endif
</div>

@push('modals')
<!-- Modals: Update Progress for Projects -->
@foreach($projects as $project)
@can('project.edit')
<div class="modal fade" id="updateProgressModal{{ $project->id }}" tabindex="-1" aria-labelledby="updateProgressModalLabel{{ $project->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 px-3 bg-light border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-1 px-2 rounded bg-primary text-white" style="font-size: 0.85rem;">
                        <i class="bi bi-speedometer2"></i>
                    </span>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0 fs-6" id="updateProgressModalLabel{{ $project->id }}">
                            Update Progress (%)
                        </h6>
                        <small class="text-muted text-truncate d-inline-block" style="max-width: 320px;">{{ $project->code }} — {{ $project->name }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="progressForm{{ $project->id }}" action="{{ route('projects.progress.update', $project) }}" method="POST" onsubmit="submitProgressAjax(event, {{ $project->id }})">
                @csrf
                @method('PATCH')
                <div class="modal-body p-3">
                    <!-- Current Progress Header Row -->
                    <div class="d-flex align-items-center justify-content-between p-2 px-3 rounded bg-light border mb-3">
                        <div>
                            <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.5px;">CURRENT PROGRESS</span>
                            <div class="fw-bold fs-4 text-primary line-height-1" id="progressDisplay{{ $project->id }}">{{ number_format($project->progress_percent, 1) }}%</div>
                        </div>
                        <div class="w-50">
                            <div class="progress" style="height: 10px; border-radius: 6px;">
                                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="progressBarDisplay{{ $project->id }}" role="progressbar" style="width: {{ $project->progress_percent }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Milestone Presets -->
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted mb-1" style="font-size: 0.75rem; text-transform: uppercase;">
                            Quick Milestone Presets (ক্লিক করে সেট করুন)
                        </label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="setProgressVal({{ $project->id }}, 25)">25%</button>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="setProgressVal({{ $project->id }}, 50)">50%</button>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-bold" onclick="setProgressVal({{ $project->id }}, 75)">75%</button>
                            <button type="button" class="btn btn-outline-success btn-sm fw-bold" onclick="setProgressVal({{ $project->id }}, 100)">100%</button>
                        </div>
                        @if(isset($project->billed_progress) && $project->billed_progress > 0)
                        <div class="mt-2 text-end">
                            <button type="button" class="btn btn-outline-info btn-sm py-1 px-2 fw-semibold" style="font-size: 0.78rem;" onclick="setProgressVal({{ $project->id }}, {{ $project->billed_progress }})">
                                <i class="bi bi-calculator me-1"></i> Auto from Certified Bills: {{ $project->billed_progress }}%
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- Input Grid -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label fw-bold small mb-1" style="font-size: 0.8rem;">
                                Percentage (0-100) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.1" min="0" max="100" name="progress_percent" id="progressInput{{ $project->id }}"
                                       class="form-control fw-bold fs-6" value="{{ (float) $project->progress_percent }}" required
                                       oninput="syncProgressDisplays({{ $project->id }}, this.value)">
                                <span class="input-group-text fw-bold">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small mb-1" style="font-size: 0.8rem;">Project Status</label>
                            <select name="status" id="projectStatus{{ $project->id }}" class="form-select form-select-sm fw-semibold">
                                <option value="planning" {{ $project->status === 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic alert response inside modal -->
                    <div id="progressAlert{{ $project->id }}" class="alert alert-success d-none py-1 px-2 small mb-0 mt-2"></div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold shadow-sm" id="progressSubmitBtn{{ $project->id }}">
                        <i class="bi bi-check2-circle me-1"></i> Save Progress
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endforeach
@endpush

@push('scripts')
<script>
function setProgressVal(projectId, val) {
    const input = document.getElementById('progressInput' + projectId);
    if (input) {
        input.value = val;
        syncProgressDisplays(projectId, val);
    }
}

function syncProgressDisplays(projectId, val) {
    const display = document.getElementById('progressDisplay' + projectId);
    const bar = document.getElementById('progressBarDisplay' + projectId);
    const statusSelect = document.getElementById('projectStatus' + projectId);
    const clampedVal = Math.max(0, Math.min(100, parseFloat(val) || 0));
    
    if (display) display.textContent = clampedVal.toFixed(1) + '%';
    if (bar) bar.style.width = clampedVal + '%';

    if (clampedVal >= 100 && statusSelect) {
        statusSelect.value = 'completed';
    }
}

function submitProgressAjax(event, projectId) {
    event.preventDefault();
    const form = document.getElementById('progressForm' + projectId);
    const btn = document.getElementById('progressSubmitBtn' + projectId);
    const alertBox = document.getElementById('progressAlert' + projectId);

    if (!form || !btn) return;

    const originalBtnHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving...';

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response error');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update table elements directly without page reload
            const tableBar = document.getElementById('tableProgressBar' + projectId);
            const tableText = document.getElementById('tableProgressText' + projectId);
            const tableStatus = document.getElementById('tableStatusBadge' + projectId);

            if (tableBar) tableBar.style.width = data.progress_percent + '%';
            if (tableText) tableText.innerHTML = data.formatted_progress + '% <i class="bi bi-pencil" style="font-size: 0.65rem;"></i>';
            if (tableStatus && data.status) {
                tableStatus.className = 'badge badge-status badge-' + data.status;
                if (data.status_label) tableStatus.textContent = data.status_label;
            }

            // Show success alert in modal
            if (alertBox) {
                alertBox.className = 'alert alert-success py-1 px-2 small mb-0 mt-2';
                alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + (data.message || 'Progress updated successfully!');
                alertBox.classList.remove('d-none');
            }

            // Close modal after short delay
            setTimeout(() => {
                const modalEl = document.getElementById('updateProgressModal' + projectId);
                if (modalEl) {
                    const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    bsModal.hide();
                }
                btn.disabled = false;
                btn.innerHTML = originalBtnHtml;
                if (alertBox) alertBox.classList.add('d-none');
            }, 600);
        } else {
            throw new Error(data.message || 'Update failed');
        }
    })
    .catch(error => {
        console.error('AJAX progress update failed, falling back to standard submit:', error);
        // Fallback: submit standard form if AJAX fails
        form.submit();
    });
}
</script>
@endpush
@endsection
