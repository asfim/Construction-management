@extends('layouts.app')

@section('title', "Expense Voucher: {$projectExpense->voucher_number}")

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace fs-6">{{ $projectExpense->voucher_number }}</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-check-circle me-1"></i> Recorded & Paid
            </span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Project Expense Voucher</h4>
        <p class="text-muted small mb-0">{{ $projectExpense->project?->name }} | Cost Code: {{ $projectExpense->costCode?->code }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2" onclick="window.print()">
            <i class="bi bi-printer"></i>
            <span>Print Voucher</span>
        </button>
        <a href="{{ route('project-expenses.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to Expenses</span>
        </a>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card card-custom border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Disbursement Payment Voucher</h5>
                    <small class="text-muted">H Tech Provision Construction ERP</small>
                </div>
                <div class="text-end">
                    <span class="text-muted small d-block">Voucher Date</span>
                    <strong class="text-dark">{{ $projectExpense->expense_date->format('d M, Y') }}</strong>
                </div>
            </div>

            <table class="table table-borderless align-middle mb-4">
                <tbody>
                    <tr>
                        <td class="text-muted" style="width: 35%;">Project Name:</td>
                        <td class="fw-bold text-dark">{{ $projectExpense->project?->name }} ({{ $projectExpense->project?->code }})</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Cost Code Head:</td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $projectExpense->costCode?->code }}</span>
                            <span class="ms-2 fw-semibold text-dark">{{ $projectExpense->costCode?->name }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payee / Recipient:</td>
                        <td class="fw-bold text-dark">{{ $projectExpense->payee_name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Payment Mode:</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase font-monospace">
                                {{ str_replace('_', ' ', $projectExpense->payment_method) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Reference / Memo No:</td>
                        <td class="text-dark">{{ $projectExpense->reference_no ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Description / Particulars:</td>
                        <td class="text-dark">{{ $projectExpense->description }}</td>
                    </tr>
                    <tr class="table-light">
                        <td class="fw-bold text-dark fs-6">Total Amount Paid:</td>
                        <td class="fw-bold text-danger font-monospace fs-4">৳ {{ number_format($projectExpense->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="row pt-4 mt-4 border-top text-center">
                <div class="col-6">
                    <small class="text-muted d-block mb-3">Prepared By:</small>
                    <strong class="text-dark d-block">{{ $projectExpense->creator?->name ?? 'Accounts Officer' }}</strong>
                    <small class="text-muted">{{ $projectExpense->created_at->format('d-m-Y H:i') }}</small>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block mb-3">Verified & Approved By:</small>
                    <strong class="text-dark d-block">{{ $projectExpense->approver?->name ?? 'Project Manager' }}</strong>
                    <small class="text-muted">{{ $projectExpense->approved_at ? $projectExpense->approved_at->format('d-m-Y H:i') : 'System Approved' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
