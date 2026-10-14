@extends('layouts.app')

@section('title', 'Issue Materials to Site')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Issue Materials to Site (Store Issue Note - SIN)</h4>
                <p class="text-muted small mb-0">Disburse warehouse materials to subcontractors, site engineers, or labor teams</p>
            </div>
            <a href="{{ route('inventory.ledger') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Stock Ledger
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('inventory.issues.store') }}" method="POST" id="issueForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->name }} ({{ $proj->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Issuing Warehouse / Store <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                        {{ $wh->name }} {{ $wh->is_central ? '(Central)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('warehouse_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Issue Voucher Number <span class="text-danger">*</span></label>
                            <input type="text" name="issue_no" class="form-control" value="{{ old('issue_no', 'SIN-' . date('Ymd') . '-' . rand(100, 999)) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Issue Date <span class="text-danger">*</span></label>
                            <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Issued To (Contractor / Gang) <span class="text-danger">*</span></label>
                            <input type="text" name="issued_to_contractor_or_labor" class="form-control" placeholder="e.g. Al-Amin Rebar Binding Gang" value="{{ old('issued_to_contractor_or_labor') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cost Code / WBS</label>
                            <select name="cost_code_id" class="form-select">
                                <option value="">Select Cost Code (Optional)</option>
                                @foreach($costCodes as $cc)
                                    <option value="{{ $cc->id }}" {{ old('cost_code_id') == $cc->id ? 'selected' : '' }}>
                                        {{ $cc->code }} - {{ $cc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Location / Purpose (Notes)</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Disbursed for 2nd floor column and beam reinforcement" value="{{ old('notes') }}">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">Issued Materials List</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Item / Material <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Quantity to Issue <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">Unit Cost (৳) <span class="text-danger">*</span></th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="issueItemsBody">
                                <tr>
                                    <td>
                                        <select name="items[0][item_id]" class="form-select form-select-sm" required>
                                            <option value="">Select Material</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}" data-rate="{{ $item->standard_rate }}">
                                                    {{ $item->name }} ({{ $item->unit?->symbol ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" name="items[0][quantity_issued]" class="form-control form-select-sm" placeholder="Quantity" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[0][unit_cost]" class="form-control form-select-sm item-rate" placeholder="Rate (৳)" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-issue-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addIssueRowBtn">
                        <i class="bi bi-plus-lg me-1"></i> Add Another Item
                    </button>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('inventory.ledger') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> Confirm & Post Issue
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
    const addBtn = document.getElementById('addIssueRowBtn');
    const tbody = document.getElementById('issueItemsBody');
    const itemOptions = `@foreach($items as $item)<option value="{{ $item->id }}" data-rate="{{ $item->standard_rate }}">{{ $item->name }} ({{ $item->unit?->symbol ?? '' }})</option>@endforeach`;

    addBtn.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm" required>
                    <option value="">Select Material</option>
                    ${itemOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.0001" min="0.0001" name="items[${rowIndex}][quantity_issued]" class="form-control form-select-sm" placeholder="Quantity" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_cost]" class="form-control form-select-sm item-rate" placeholder="Rate (৳)" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-issue-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);
        rowIndex++;
        updateBtns();
    });

    tbody.addEventListener('change', function (e) {
        if (e.target.tagName === 'SELECT') {
            const opt = e.target.options[e.target.selectedIndex];
            const defaultRate = opt.getAttribute('data-rate');
            const rateInput = e.target.closest('tr').querySelector('.item-rate');
            if (defaultRate && rateInput && !rateInput.value) {
                rateInput.value = parseFloat(defaultRate).toFixed(2);
            }
        }
    });

    tbody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-issue-row')) {
            e.target.closest('tr').remove();
            updateBtns();
        }
    });

    function updateBtns() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(r => {
            const btn = r.querySelector('.remove-issue-row');
            if (btn) btn.disabled = (rows.length === 1);
        });
    }
});
</script>
@endsection
