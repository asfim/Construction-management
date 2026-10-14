@extends('layouts.app')

@section('title', 'Record Project Expense Voucher')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Record Project Expense Voucher</h4>
        <p class="text-muted small mb-0">Record petty cash, site expenses, local fuel, and cost code disbursements</p>
    </div>
    <a href="{{ route('project-expenses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back to Expenses</span>
    </a>
</div>

<div class="card card-custom border-0 shadow-sm col-12 col-lg-8 mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('project-expenses.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select" required>
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $activeProjectId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->code }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Cost Code Head <span class="text-danger">*</span></label>
                    <select name="cost_code_id" class="form-select" required>
                        <option value="">Select Cost Code Head</option>
                        @foreach($costCodes as $cc)
                        <option value="{{ $cc->id }}">
                            {{ $cc->code }} — {{ $cc->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Voucher Number <span class="text-danger">*</span></label>
                    <input type="text" name="voucher_number" class="form-control font-monospace" value="{{ old('voucher_number', $nextVoucherNo) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Payee / Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="payee_name" class="form-control" placeholder="e.g. Local Diesel Supplier / Daily Labor Team" value="{{ old('payee_name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Payment Method <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">Petty Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="bkash_nagad">bKash / Nagad / MFS</option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Amount (BDT ৳) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">৳</span>
                        <input type="number" step="0.01" name="amount" class="form-control font-monospace" placeholder="0.00" value="{{ old('amount') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Receipt / Bill Memo Ref</label>
                    <input type="text" name="reference_no" class="form-control" placeholder="Cash memo or invoice number" value="{{ old('reference_no') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Description of Expense <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Purpose of expenditure and site verification..." required>{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Save & Post Expense Voucher
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
