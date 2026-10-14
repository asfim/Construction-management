@extends('layouts.app')

@section('title', 'Cost Codes & WBS Structure')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cost Codes & WBS Structure</h4>
        <p class="text-muted small mb-0">Construction cost breakdown classification and work breakdown structure</p>
    </div>
    @can('cost-code.manage')
    <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newCostCodeModal">
        <i class="bi bi-plus-lg"></i>
        <span>Add New Cost Code</span>
    </button>
    @endcan
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th style="width: 160px;">Cost Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costCodes as $code)
                    <tr class="table-light">
                        <td>
                            <strong class="font-monospace text-primary fs-6">{{ $code->code }}</strong>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $code->name }}</strong>
                        </td>
                        <td class="text-muted small">{{ $code->description ?? '—' }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        </td>
                    </tr>
                        @foreach($code->children as $child)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border font-monospace ms-2">↳ {{ $child->code }}</span>
                            </td>
                            <td class="ps-4">
                                <span class="text-dark">{{ $child->name }}</span>
                            </td>
                            <td class="text-muted small">{{ $child->description ?? '—' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success">Active</span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            No cost codes found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Cost Code -->
<div class="modal fade" id="newCostCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cost-codes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Cost Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Parent Cost Code (Optional)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">Add as Root Category</option>
                            @foreach($costCodes as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="01.01-EXCAVATION" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Excavation Work" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Secondary / Local Name</label>
                        <input type="text" name="name_bn" class="form-control" placeholder="Alternative title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Cost Code</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
