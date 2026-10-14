@extends('layouts.app')

@section('title', 'Create Material Requisition')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Create Material Requisition</h4>
                <p class="text-muted small mb-0">Specify required construction materials, quantities, and target project</p>
            </div>
            <a href="{{ route('material-requisitions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('material-requisitions.store') }}" method="POST" id="mrForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">MR Number <span class="text-danger">*</span></label>
                            <input type="text" name="requisition_no" class="form-control @error('requisition_no') is-invalid @enderror"
                                value="{{ old('requisition_no', 'MR-' . date('Ymd') . '-' . rand(100, 999)) }}" required>
                            @error('requisition_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Requisition Date <span class="text-danger">*</span></label>
                            <input type="date" name="requisition_date" class="form-control @error('requisition_date') is-invalid @enderror"
                                value="{{ old('requisition_date', date('Y-m-d')) }}" required>
                            @error('requisition_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Required By Date</label>
                            <input type="date" name="required_by_date" class="form-control"
                                value="{{ old('required_by_date', date('Y-m-d', strtotime('+7 days'))) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Usage Purpose / Notes</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="e.g. Required for 2nd floor roof slab casting and shear wall reinforcement">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Requisition Items</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Item / Material <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Cost Code / WBS</th>
                                    <th style="width: 20%;">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">Remarks</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td>
                                        <select name="items[0][item_id]" class="form-select form-select-sm" required>
                                            <option value="">Select Material</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }} ({{ $item->unit?->symbol ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[0][cost_code_id]" class="form-select form-select-sm">
                                            <option value="">Select Cost Code (Optional)</option>
                                            @foreach($costCodes as $cc)
                                                <option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" name="items[0][quantity_requested]" class="form-control form-select-sm" placeholder="Quantity" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][remarks]" class="form-control form-select-sm" placeholder="Remarks">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addRowBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Another Item
                    </button>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('material-requisitions.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Submit Requisition (MR)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const addRowBtn = document.getElementById('addRowBtn');
    const itemsBody = document.getElementById('itemsBody');

    const itemOptions = `@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit?->symbol ?? '' }})</option>@endforeach`;
    const ccOptions = `@foreach($costCodes as $cc)<option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>@endforeach`;

    addRowBtn.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm" required>
                    <option value="">Select Material</option>
                    ${itemOptions}
                </select>
            </td>
            <td>
                <select name="items[${rowIndex}][cost_code_id]" class="form-select form-select-sm">
                    <option value="">Select Cost Code (Optional)</option>
                    ${ccOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.0001" min="0.0001" name="items[${rowIndex}][quantity_requested]" class="form-control form-select-sm" placeholder="Quantity" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][remarks]" class="form-control form-select-sm" placeholder="Remarks">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        itemsBody.appendChild(row);
        rowIndex++;
        updateRemoveButtons();
    });

    itemsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = itemsBody.querySelectorAll('tr');
        rows.forEach(r => {
            const btn = r.querySelector('.remove-row');
            if (btn) btn.disabled = (rows.length === 1);
        });
    }
});
</script>
@endsection
