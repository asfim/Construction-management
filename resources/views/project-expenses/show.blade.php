@extends('layouts.app')

@section('title', "Expense Voucher: {$projectExpense->voucher_number}")

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 d-print-none">
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
    <div class="col-12 col-lg-9">
        <div class="card card-custom border-0 shadow-sm p-4 p-md-5 bg-white">
            <!-- Print Header with Admin Company Bindings -->
            <x-print-header 
                :company="$projectExpense->project?->company" 
                title="Expense Voucher" 
                subtitle="Site Cash Disbursement & Cost Accounting Payment Voucher"
                :docNumber="$projectExpense->voucher_number"
                docNumberLabel="Voucher No"
                :docDate="$projectExpense->expense_date->format('d-m-Y')"
                docDateLabel="Voucher Date"
                status="Recorded & Paid"
                statusClass="bg-success"
                :project="$projectExpense->project"
                :extraMeta="['Cost Code' => $projectExpense->costCode?->code . ' - ' . $projectExpense->costCode?->name, 'Payment Mode' => str_replace('_', ' ', strtoupper($projectExpense->payment_method))]"
            />

            <table class="table table-bordered align-middle mb-4">
                <tbody>
                    <tr>
                        <td class="text-muted" style="width: 30%;">Payee / Recipient:</td>
                        <td class="fw-bold text-dark fs-6">{{ $projectExpense->payee_name }}</td>
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
                        <td class="text-muted">Reference / Memo / Bill No:</td>
                        <td class="text-dark font-monospace fw-semibold">{{ $projectExpense->reference_no ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Description / Particulars:</td>
                        <td class="text-dark">{{ $projectExpense->description }}</td>
                    </tr>
                    <tr class="table-primary">
                        <td class="fw-bold text-dark fs-6">Total Amount Paid:</td>
                        <td class="fw-bold text-primary font-monospace fs-4">
                            {{ $projectExpense->project?->company?->currency_symbol ?? '৳' }} {{ number_format($projectExpense->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Official Signatures -->
            <x-print-footer 
                :company="$projectExpense->project?->company"
                :preparedBy="$projectExpense->creator?->name ?? 'Accounts Officer'"
                preparedTitle="Accounts Officer / Cashier"
                :preparedDate="$projectExpense->created_at->format('d-m-Y H:i')"
                :checkedBy="$projectExpense->approver?->name ?? 'Project Manager'"
                checkedTitle="Project Manager / Approver"
                :approvedDate="$projectExpense->approved_at ? $projectExpense->approved_at->format('d-m-Y H:i') : null"
                :approvedBy="null"
                :clientBy="$projectExpense->payee_name"
                clientTitle="Payee / Recipient Signature"
                notes="Received the above mentioned sum in full and final settlement against certified project expenditure."
                :showWatermark="true"
            />
        </div>
    </div>
</div>
@endsection
