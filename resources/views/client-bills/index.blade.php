@extends('layouts.app')

@section('title', 'Client Billing (RA Bills / IPC)')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Client Billing & Interim Payment Certificates (RA Bills)</h4>
        <p class="text-muted small mb-0">Running Account (RA) bills, certified progress quantities, advance recovery, and retention balance</p>
    </div>
    @can('bill.create')
    <a href="{{ route('client-bills.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>Create RA Bill</span>
    </a>
    @endcan
</div>

<!-- Filters -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('client-bills.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="submitted" {{ $status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="certified" {{ $status == 'certified' ? 'selected' : '' }}>Certified</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('client-bills.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bills Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Bill Number</th>
                        <th>Project</th>
                        <th>Billing Period</th>
                        <th class="text-end">Gross Amount (৳)</th>
                        <th class="text-end">Deductions (৳)</th>
                        <th class="text-end">Net Payable (৳)</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                    <tr>
                        <td>
                            <a href="{{ route('client-bills.show', $bill) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                {{ $bill->bill_number }}
                            </a>
                            <small class="text-muted d-block">{{ $bill->bill_date->format('d-m-Y') }}</small>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $bill->project?->name }}</strong>
                            <small class="text-muted">{{ $bill->project?->client_name ?? 'Client' }}</small>
                        </td>
                        <td>
                            <small class="text-secondary fw-semibold">
                                {{ $bill->period_start->format('d-m-Y') }} to {{ $bill->period_end->format('d-m-Y') }}
                            </small>
                        </td>
                        <td class="text-end font-monospace text-dark fw-semibold">
                            ৳ {{ number_format($bill->gross_amount, 2) }}
                        </td>
                        <td class="text-end font-monospace text-danger small">
                            - ৳ {{ number_format($bill->advance_recovery_amount + $bill->retention_amount, 2) }}
                            <small class="text-muted d-block">({{ $bill->advance_recovery_percentage }}% Adv / {{ $bill->retention_percentage }}% Ret)</small>
                        </td>
                        <td class="text-end font-monospace fw-bold text-success fs-6">
                            ৳ {{ number_format($bill->net_payable, 2) }}
                        </td>
                        <td>
                            @if($bill->status === 'paid')
                            <span class="badge bg-success text-white">Paid</span>
                            @elseif($bill->status === 'certified')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                <i class="bi bi-patch-check-fill me-1"></i> Certified
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                <i class="bi bi-clock me-1"></i> Submitted
                            </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('client-bills.show', $bill) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View Certificate
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No Running Account bills recorded yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bills->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endsection
