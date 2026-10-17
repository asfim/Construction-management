@extends('layouts.app')

@section('title', 'BOQ Revisions & Variations — ' . $project->name)

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
            <span class="badge badge-status badge-{{ $project->status }}">{{ __('erp.status.' . $project->status) }}</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">BOQ & Estimation Revisions</h4>
        <p class="text-muted small mb-0">{{ $project->name }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @canany(['boq.manage', 'boq.approve'])
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createBoqModal">
            <i class="bi bi-plus-lg"></i>
            <span>{{ $boqVersions->isEmpty() ? 'Initialize Project BOQ' : 'New BOQ Version' }}</span>
        </button>
        @endcanany
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Project</span>
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Version No</th>
                        <th>Title & Remarks</th>
                        <th>Total Items</th>
                        <th>Total BOQ Amount (BDT)</th>
                        <th>Approval & Lock Status</th>
                        <th>Prepared / Approved By</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boqVersions as $ver)
                    <tr>
                        <td>
                            <span class="badge bg-primary fs-6 px-3 py-2">Version {{ $ver->version_number }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $ver->title }}</strong>
                            <small class="text-muted">{{ $ver->remarks ?? 'No remarks provided' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $ver->items_count }} Items</span>
                        </td>
                        <td class="fw-bold fs-6 text-dark">
                            ৳ {{ number_format($ver->total_amount, 2) }}
                        </td>
                        <td>
                            @if($ver->is_locked)
                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                <i class="bi bi-lock-fill me-1"></i> Approved & Locked (Immutable)
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2">
                                <i class="bi bi-pencil me-1"></i> Draft (Editable)
                            </span>
                            @endif
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>Created:</strong> {{ $ver->creator?->name ?? 'System' }}</small>
                            @if($ver->approved_by)
                            <small class="d-block text-success"><strong>Approved:</strong> {{ $ver->approver?->name }} ({{ $ver->approved_at?->format('d-m-Y') }})</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <a href="{{ route('projects.boqs.show', [$project, $ver]) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-table me-1"></i> Open BOQ
                                </a>
                                @can('boq.manage')
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-edit-ver"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBoqIndexModal"
                                    data-version="{{ $ver->version_number }}"
                                    data-title="{{ $ver->title }}"
                                    data-remarks="{{ $ver->remarks }}"
                                    data-action="{{ route('projects.boqs.update', [$project, $ver]) }}"
                                    title="Edit BOQ Version Details">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if(! $ver->is_locked || auth()->user()->hasRole(['Super Admin', 'Company Owner']))
                                <form action="{{ route('projects.boqs.destroy', [$project, $ver]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete BOQ Version {{ $ver->version_number }} ({{ $ver->title }})? All line items and rate analyses under this version will be removed permanently.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete BOQ Version">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-spreadsheet fs-1 text-secondary d-block mb-3"></i>
                            <h5 class="fw-bold text-dark mb-1">No BOQ records found for this project</h5>
                            <p class="text-muted small mb-3">Start by initializing Version 1 (Baseline Contract Estimation) to add line items and rates.</p>
                            @canany(['boq.manage', 'boq.approve'])
                            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createBoqModal">
                                <i class="bi bi-plus-circle me-1"></i> Initialize Project BOQ (Version 1)
                            </button>
                            @endcanany
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modals')
@canany(['boq.manage', 'boq.approve'])
<div class="modal fade" id="createBoqModal" tabindex="-1" aria-labelledby="createBoqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-1 px-2 rounded bg-primary text-white">
                        <i class="bi bi-file-earmark-plus"></i>
                    </span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="createBoqModalLabel">
                        {{ $boqVersions->isEmpty() ? 'Initialize Baseline BOQ' : 'Create New BOQ Version' }}
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.boqs.store', $project) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Version Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="e.g. Original Approved Baseline BOQ"
                               value="{{ old('title', $boqVersions->isEmpty() ? 'Original Approved Baseline BOQ' : 'BOQ Version ' . ($boqVersions->max('version_number') + 1)) }}"
                               required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Scope / Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"
                                  placeholder="Describe the scope, tender package, or estimation baseline notes...">{{ old('remarks', $boqVersions->isEmpty() ? 'Initial approved contract baseline estimation and specifications.' : '') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Create & Start Adding Items
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@can('boq.manage')
<div class="modal fade" id="editBoqIndexModal" tabindex="-1" aria-labelledby="editBoqIndexModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-1 px-2 rounded bg-primary text-white">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editBoqIndexModalLabel">
                        Edit BOQ Version Details
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBoqIndexForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Version Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_ver_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Scope / Remarks</label>
                        <textarea name="remarks" id="edit_ver_remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Update Version
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endcanany
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('editBoqIndexModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        document.getElementById('editBoqIndexForm').action = button.dataset.action || '';
        document.getElementById('edit_ver_title').value = button.dataset.title || '';
        document.getElementById('edit_ver_remarks').value = button.dataset.remarks || '';
        const titleEl = document.getElementById('editBoqIndexModalLabel');
        if (titleEl) {
            titleEl.textContent = 'Edit BOQ Version ' + (button.dataset.version || '') + ' Details';
        }
    });
});
</script>
@endpush
@endsection
