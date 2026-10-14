@extends('layouts.app')

@section('title', 'Create Running Account (RA) Bill')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Create Running Account (RA) Bill</h4>
        <p class="text-muted small mb-0">Compute cumulative work progress, contract deductions, and interim certificate valuation</p>
    </div>
    <a href="{{ route('client-bills.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back to List</span>
    </a>
</div>

<form action="{{ route('client-bills.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- Billing Parameters -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Bill Header Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select" onchange="window.location.href='{{ route('client-bills.create') }}?project_id=' + this.value" required>
                                @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold small">Bill / IPC Number <span class="text-danger">*</span></label>
                            <input type="text" name="bill_number" class="form-control font-monospace" value="{{ old('bill_number', $nextBillNumber) }}" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold small">Bill Date <span class="text-danger">*</span></label>
                            <input type="date" name="bill_date" class="form-control" value="{{ old('bill_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Measurement Period Start <span class="text-danger">*</span></label>
                            <input type="date" name="period_start" class="form-control" value="{{ old('period_start', date('Y-m-01')) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Measurement Period End <span class="text-danger">*</span></label>
                            <input type="date" name="period_end" class="form-control" value="{{ old('period_end', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Remarks / Contract Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="e.g. 1st Running Account Bill for substructure and 1st floor columns...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOQ Line Items Measurement Table -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calculator text-success me-2"></i> BOQ Line Items Measurement
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th>Item & Description</th>
                                    <th>Unit</th>
                                    <th class="text-end">Rate (৳)</th>
                                    <th class="text-end">Prev Qty</th>
                                    <th class="text-end" style="width: 140px;">Current Qty <span class="text-danger">*</span></th>
                                    <th class="text-end">Current Amount (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($boqItems as $index => $item)
                                @php
                                    $prevQty = $previousQuantities[$item->id] ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][boq_item_id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][previous_quantity]" value="{{ $prevQty }}">
                                        <input type="hidden" name="items[{{ $index }}][unit_rate]" id="rate_{{ $index }}" value="{{ $item->unit_rate }}">
                                        <span class="badge bg-light text-dark border font-monospace">{{ $item->item_code }}</span>
                                        <strong class="text-dark small d-block mt-1">{{ $item->description }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $item->unit }}</span>
                                    </td>
                                    <td class="text-end font-monospace">
                                        ৳ {{ number_format($item->unit_rate, 2) }}
                                    </td>
                                    <td class="text-end font-monospace text-muted">
                                        {{ number_format($prevQty, 4) }}
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="items[{{ $index }}][current_quantity]" id="qty_{{ $index }}" class="form-control form-control-sm text-end current-qty-input" value="0.0000" min="0" oninput="calculateBill()" required>
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-dark" id="amt_{{ $index }}">
                                        ৳ 0.00
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No BOQ line items available for this project. Please add BOQ items first.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Deductions & Calculation Summary -->
        <div class="col-12 col-lg-4">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-receipt-cutoff text-warning me-2"></i> Bill Financial Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Gross Measured Work (৳)</label>
                        <h4 class="fw-bold text-dark font-monospace mb-0" id="summaryGross">৳ 0.00</h4>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Advance Mobilization Recovery (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="advance_recovery_percentage" id="advancePct" class="form-control" value="{{ old('advance_recovery_percentage', $project?->advance_recovery_percentage ?? 10.0) }}" oninput="calculateBill()" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <small class="text-danger d-block mt-1" id="summaryAdv">- ৳ 0.00</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Security Retention Withheld (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="retention_percentage" id="retentionPct" class="form-control" value="{{ old('retention_percentage', $project?->retention_percentage ?? 5.0) }}" oninput="calculateBill()" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <small class="text-danger d-block mt-1" id="summaryRet">- ৳ 0.00</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">VAT (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="vat_percentage" id="vatPct" class="form-control" value="{{ old('vat_percentage', '7.5') }}" oninput="calculateBill()" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                        <small class="text-success d-block mt-1" id="summaryVat">+ ৳ 0.00</small>
                    </div>

                    <div class="p-3 bg-primary-subtle rounded-3 mt-4 text-center">
                        <span class="text-muted small fw-bold d-block text-uppercase mb-1">Net Certified Payable (৳)</span>
                        <h3 class="fw-bold text-primary font-monospace mb-0" id="summaryNet">৳ 0.00</h3>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" {{ $boqItems->isEmpty() ? 'disabled' : '' }}>
                <i class="bi bi-check2-circle me-1"></i> Submit Running Account Bill
            </button>
        </div>
    </div>
</form>

<script>
function calculateBill() {
    let count = {{ $boqItems->count() }};
    let gross = 0;

    for (let i = 0; i < count; i++) {
        let qtyElem = document.getElementById('qty_' + i);
        let rateElem = document.getElementById('rate_' + i);
        let amtElem = document.getElementById('amt_' + i);

        if (qtyElem && rateElem && amtElem) {
            let qty = parseFloat(qtyElem.value) || 0;
            let rate = parseFloat(rateElem.value) || 0;
            let amt = qty * rate;
            gross += amt;
            amtElem.textContent = '৳ ' + amt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    let advPct = parseFloat(document.getElementById('advancePct').value) || 0;
    let retPct = parseFloat(document.getElementById('retentionPct').value) || 0;
    let vatPct = parseFloat(document.getElementById('vatPct').value) || 0;

    let advAmt = gross * (advPct / 100);
    let retAmt = gross * (retPct / 100);
    let vatAmt = gross * (vatPct / 100);
    let net = gross - advAmt - retAmt + vatAmt;

    document.getElementById('summaryGross').textContent = '৳ ' + gross.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('summaryAdv').textContent = '- ৳ ' + advAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('summaryRet').textContent = '- ৳ ' + retAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('summaryVat').textContent = '+ ৳ ' + vatAmt.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('summaryNet').textContent = '৳ ' + net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script>
@endsection
