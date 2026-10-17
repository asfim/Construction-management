@extends('layouts.app')

@section('title', "BOQ Version {$boq->version_number} — {$project->name}")

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary px-3 py-1 font-monospace fs-6">BOQ Version {{ $boq->version_number }}</span>
            @if($boq->is_locked)
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-lock-fill me-1"></i> Approved & Locked (Immutable)
            </span>
            @else
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <i class="bi bi-pencil me-1"></i> Draft (Editable)
            </span>
            @endif
        </div>
        <h4 class="fw-bold mb-1 text-dark">{{ $boq->title }}</h4>
        <p class="text-muted small mb-0">{{ $project->name }} ({{ $project->code }})</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if(! $boq->is_locked)
            @can('boq.approve')
            <form action="{{ route('projects.boqs.approve', [$project, $boq]) }}" method="POST" onsubmit="return confirm('Approving will lock this BOQ version permanently against further edits. Proceed?');">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-check2-circle"></i>
                    <span>Approve & Lock</span>
                </button>
            </form>
            @endcan
        @else
            @can('boq.approve')
            <form action="{{ route('projects.boqs.unlock', [$project, $boq]) }}" method="POST" class="d-inline" onsubmit="return confirm('Unlock this BOQ version to allow editing line items, adding new items, and adjusting quantities?');">
                @csrf
                <button type="submit" class="btn btn-outline-warning text-dark d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-unlock"></i>
                    <span>Unlock / Edit</span>
                </button>
            </form>
            @endcan
            @can('boq.manage')
            <form action="{{ route('projects.boqs.revision', [$project, $boq]) }}" method="POST" onsubmit="return confirm('Create a new revision variation while preserving this locked baseline?');">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-copy"></i>
                    <span>Create Variation Revision</span>
                </button>
            </form>
            @endcan
        @endif
        @can('boq.manage')
        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#editBoqModal">
            <i class="bi bi-pencil-square"></i>
            <span>Edit Version</span>
        </button>
        @if(! $boq->is_locked || auth()->user()->hasRole(['Super Admin', 'Company Owner']))
        <form action="{{ route('projects.boqs.destroy', [$project, $boq]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete BOQ Version {{ $boq->version_number }} ({{ $boq->title }})? All line items and rate analyses will be permanently removed.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-trash"></i>
                <span>Delete Version</span>
            </button>
        </form>
        @endif
        @endcan
        <a href="{{ route('projects.boqs.index', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Revisions List</span>
        </a>
    </div>
</div>

<!-- Financial Summary Bar -->
<div class="card card-custom border-0 shadow-sm p-3 mb-4 bg-light">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <span class="text-muted small d-block">Total Estimated BOQ Value:</span>
            <h3 class="fw-bold text-dark mb-0">৳ {{ number_format($boq->total_amount, 2) }}</h3>
        </div>
        <div class="d-flex align-items-center gap-4 text-muted small">
            <div>
                <strong>Contract Value:</strong> ৳ {{ number_format($project->contract_value, 2) }}
            </div>
            <div>
                <strong>Total Line Items:</strong> {{ $boq->items->count() }}
            </div>
        </div>
    </div>
</div>

<!-- BOQ Items Table -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-check text-primary me-2"></i> BOQ Line Items Specification
        </h6>
        @can('boq.manage')
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-lg me-1"></i> Add Line Item
        </button>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th style="width: 120px;">Item Code</th>
                        <th>Work Description & Specification</th>
                        <th>Cost Code</th>
                        <th>Unit</th>
                        <th class="text-end">Planned Quantity</th>
                        <th class="text-end">Unit Rate (৳)</th>
                        <th class="text-end">Total Amount (৳)</th>
                        <th class="text-center" style="width: 140px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boq->items as $item)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $item->item_code }}</span>
                        </td>
                        <td>
                            <p class="mb-0 text-dark fw-medium small">{{ $item->description }}</p>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary small">
                                {{ $item->costCode?->code ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $item->unit }}</span>
                        </td>
                        <td class="text-end font-monospace fw-semibold text-dark">
                            {{ number_format($item->planned_quantity, 4) }}
                        </td>
                        <td class="text-end font-monospace text-dark">
                            ৳ {{ number_format($item->unit_rate, 2) }}
                        </td>
                        <td class="text-end font-monospace fw-bold text-dark">
                            ৳ {{ number_format($item->total_amount, 2) }}
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-1">
                                <a href="{{ route('boq-items.rate-analysis', $item) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Rate Breakdown">
                                    <i class="bi bi-calculator me-1"></i> Analysis
                                </a>
                                @can('boq.manage')
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-edit-item"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editItemModal"
                                    data-id="{{ $item->id }}"
                                    data-code="{{ $item->item_code }}"
                                    data-cost-code="{{ $item->cost_code_id }}"
                                    data-unit="{{ $item->unit }}"
                                    data-description="{{ $item->description }}"
                                    data-quantity="{{ $item->planned_quantity }}"
                                    data-rate="{{ $item->unit_rate }}"
                                    data-action="{{ route('projects.boqs.items.update', [$project, $boq, $item]) }}"
                                    title="Edit Line Item">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if(! $boq->is_locked || auth()->user()->hasRole(['Super Admin', 'Company Owner']))
                                <form action="{{ route('projects.boqs.items.destroy', [$project, $boq, $item]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete line item {{ $item->item_code }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Delete Item">
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
                        <td colspan="8" class="text-center py-5 text-muted">
                            No line items in this BOQ.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($boq->items->isNotEmpty())
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="text-end">Total Estimated Value:</td>
                        <td class="text-end text-primary fs-6">৳ {{ number_format($boq->total_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal: Add Item to BOQ -->
@can('boq.manage')
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('projects.boqs.items.store', [$project, $boq]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New BOQ Line Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Item Code <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" class="form-control" placeholder="01.01.001" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Cost Code</label>
                            <select name="cost_code_id" class="form-select">
                                <option value="">Select Cost Code</option>
                                @foreach($costCodes as $cc)
                                <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Unit of Measure <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" placeholder="cft, sft, ton, etc." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Description of Work <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Civil work specification and scope..." required></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Planned Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="planned_quantity" class="form-control" placeholder="0.0000" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Unit Rate (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="unit_rate" class="form-control" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<!-- Modal: Edit BOQ Version -->
@can('boq.manage')
<div class="modal fade" id="editBoqModal" tabindex="-1" aria-labelledby="editBoqModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="p-1 px-2 rounded bg-primary text-white">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="editBoqModalLabel">
                        Edit BOQ Version {{ $boq->version_number }} Details
                    </h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('projects.boqs.update', [$project, $boq]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Version Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $boq->title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Scope / Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ old('remarks', $boq->remarks) }}</textarea>
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

<!-- Modal: Edit Line Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-light border-bottom py-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-1 px-2 rounded bg-primary text-white">
                            <i class="bi bi-pencil-square"></i>
                        </span>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="editItemModalLabel">Edit BOQ Line Item</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Item Code <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" id="edit_item_code" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Cost Code</label>
                            <select name="cost_code_id" id="edit_cost_code_id" class="form-select">
                                <option value="">Select Cost Code</option>
                                @foreach($costCodes as $cc)
                                <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Unit of Measure <span class="text-danger">*</span></label>
                            <input type="text" name="unit" id="edit_unit" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Full Description of Work <span class="text-danger">*</span></label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Planned Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="planned_quantity" id="edit_planned_quantity" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Unit Rate (৳) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="unit_rate" id="edit_unit_rate" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModalEl = document.getElementById('editItemModal');
    if (!editModalEl) return;

    editModalEl.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        document.getElementById('editItemForm').action = button.dataset.action || '';
        document.getElementById('edit_item_code').value = button.dataset.code || '';
        document.getElementById('edit_cost_code_id').value = button.dataset.costCode || '';
        document.getElementById('edit_unit').value = button.dataset.unit || '';
        document.getElementById('edit_description').value = button.dataset.description || '';
        document.getElementById('edit_planned_quantity').value = button.dataset.quantity || '';
        document.getElementById('edit_unit_rate').value = button.dataset.rate || '';
    });
});
</script>
@endpush
