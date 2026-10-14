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
        @if(! $boq->is_locked)
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-lg me-1"></i> Add Line Item
        </button>
        @endif
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
                        <th class="text-center">Rate Analysis</th>
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
                            <a href="{{ route('boq-items.rate-analysis', $item) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="View Rate Breakdown">
                                <i class="bi bi-calculator me-1"></i> Analysis
                            </a>
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

<!-- Modal: Add Item to Unlocked BOQ -->
@if(! $boq->is_locked)
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
@endif
@endsection
