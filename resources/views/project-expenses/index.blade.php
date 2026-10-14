@extends('layouts.app')

@section('title', 'Project Expenses & Site Cashbook')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Project Expenses & Site Cashbook</h4>
        <p class="text-muted small mb-0">Record project operational expenses, petty cash vouchers, and site cashbook disbursements</p>
    </div>
    @can('budget.manage')
    <a href="{{ route('project-expenses.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>Record Expense Voucher</span>
    </a>
    @endcan
</div>

<!-- Financial Summary Bar -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Total Recorded Expenses</span>
            <h4 class="fw-bold text-danger font-monospace mb-0">৳ {{ number_format($totalExpenseAmount, 2) }}</h4>
            <small class="text-muted">Total actual cash & site expenditure</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Cost Control Tracking</span>
            <h5 class="fw-bold text-primary mb-0"><i class="bi bi-link-45deg me-1"></i> Budget Linked</h5>
            <small class="text-muted">Automatically updates project budget variance</small>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">Voucher Audit Count</span>
            <h5 class="fw-bold text-dark mb-0">{{ $expenses->total() }} Vouchers</h5>
            <small class="text-muted">Immutable expense audit record</small>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('project-expenses.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <select name="cost_code_id" class="form-select">
                    <option value="">All Cost Codes</option>
                    @foreach($costCodes as $cc)
                    <option value="{{ $cc->id }}" {{ $costCodeId == $cc->id ? 'selected' : '' }}>
                        {{ $cc->code }} — {{ $cc->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('project-expenses.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Voucher No</th>
                        <th>Project</th>
                        <th>Cost Code</th>
                        <th>Date</th>
                        <th>Payee & Purpose</th>
                        <th>Payment Method</th>
                        <th class="text-end">Amount (৳)</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td>
                            <a href="{{ route('project-expenses.show', $exp) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                {{ $exp->voucher_number }}
                            </a>
                            @if($exp->reference_no)
                            <small class="text-muted d-block">Ref: {{ $exp->reference_no }}</small>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $exp->project?->name }}</strong>
                            <small class="text-muted">{{ $exp->project?->code }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">
                                {{ $exp->costCode?->code }}
                            </span>
                            <small class="text-muted d-block">{{ $exp->costCode?->name }}</small>
                        </td>
                        <td class="text-secondary fw-semibold">
                            {{ $exp->expense_date->format('d-m-Y') }}
                        </td>
                        <td>
                            <strong class="text-dark small d-block">{{ $exp->payee_name }}</strong>
                            <small class="text-muted text-truncate d-inline-block" style="max-width: 220px;">{{ $exp->description }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.72rem;">
                                {{ str_replace('_', ' ', $exp->payment_method) }}
                            </span>
                        </td>
                        <td class="text-end font-monospace fw-bold text-danger fs-6">
                            ৳ {{ number_format($exp->amount, 2) }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('project-expenses.show', $exp) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No project expenses recorded.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($expenses->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $expenses->links() }}
    </div>
    @endif
</div>
@endsection
