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

<!-- Filters & Approval Workflow Tabs -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <!-- Client Bill Approval Tabs -->
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ in_array($status, ['unapproved', 'submitted']) ? 'active' : '' }}" 
                       href="{{ route('client-bills.index', ['status' => 'submitted']) }}">
                        <i class="bi bi-clock-history me-1"></i>
                        <span>Pending Certification</span>
                        <span class="badge {{ in_array($status, ['unapproved', 'submitted']) ? 'bg-white text-primary' : 'bg-warning-subtle text-warning-emphasis' }} ms-1 rounded-pill">
                            {{ $counts['unapproved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ in_array($status, ['approved', 'certified', 'paid']) ? 'active' : '' }}" 
                       href="{{ route('client-bills.index', ['status' => 'approved']) }}">
                        <i class="bi bi-patch-check me-1"></i>
                        <span>Certified / Paid</span>
                        <span class="badge {{ in_array($status, ['approved', 'certified', 'paid']) ? 'bg-white text-primary' : 'bg-success-subtle text-success' }} ms-1 rounded-pill">
                            {{ $counts['approved'] }}
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-2 px-3 {{ $status === 'all' ? 'active' : '' }}" 
                       href="{{ route('client-bills.index', ['status' => 'all']) }}">
                        <i class="bi bi-collection me-1"></i>
                        <span>All Bills</span>
                        <span class="badge {{ $status === 'all' ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }} ms-1 rounded-pill">
                            {{ $counts['all'] }}
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Specific Filter Dropdown -->
            <form method="GET" action="{{ route('client-bills.index') }}" class="d-flex align-items-center gap-2">
                <label class="form-label small text-muted mb-0 text-nowrap">Filter By:</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 170px;">
                    <option value="submitted" {{ in_array($status, ['submitted', 'unapproved']) ? 'selected' : '' }}>Pending Certification</option>
                    <option value="certified" {{ $status == 'certified' ? 'selected' : '' }}>Certified</option>
                    <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                </select>
                @if(! in_array($status, ['unapproved', 'submitted']))
                <a href="{{ route('client-bills.index') }}" class="btn btn-sm btn-outline-secondary text-nowrap" title="Reset to Pending Certification">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>
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
                                <i class="bi bi-clock me-1"></i> Submitted (Pending)
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
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            @if(in_array($status, ['unapproved', 'submitted']))
                                <span>No client bills currently pending certification.</span>
                                <div class="mt-2">
                                    <a href="{{ route('client-bills.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">
                                        View All Bills ({{ $counts['all'] }})
                                    </a>
                                </div>
                            @elseif(in_array($status, ['approved', 'certified', 'paid']))
                                <span>No certified or paid client bills found.</span>
                            @else
                                <span>No Running Account bills recorded yet.</span>
                            @endif
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
