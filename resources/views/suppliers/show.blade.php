@extends('layouts.app')

@section('title', $supplier->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $supplier->code }}</span>
            <span class="badge bg-primary-subtle text-primary">{{ $supplier->payment_terms_days }} Days Credit</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">{{ $supplier->name }}</h4>
        <p class="text-muted small mb-0">{{ $supplier->city }}</p>
    </div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i> Contact & General Details
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 40%;">Contact Person:</td>
                            <td class="fw-semibold text-dark">{{ $supplier->contact_person ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone Number:</td>
                            <td class="text-dark">{{ $supplier->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email Address:</td>
                            <td class="text-dark">{{ $supplier->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Office Address:</td>
                            <td class="text-dark">{{ $supplier->address ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Credit Terms:</td>
                            <td class="text-dark fw-bold">{{ $supplier->payment_terms_days }} Days</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bank text-success me-2"></i> Tax & Banking Credentials
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 40%;">Trade License No:</td>
                            <td class="fw-semibold text-dark">{{ $supplier->trade_license ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">e-TIN:</td>
                            <td class="fw-semibold text-dark">{{ $supplier->tin ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">VAT BIN:</td>
                            <td class="fw-semibold text-dark">{{ $supplier->bin ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Bank Name:</td>
                            <td class="text-dark">{{ $supplier->bank_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Account Number:</td>
                            <td class="text-dark font-monospace">{{ $supplier->bank_account_no ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Branch Name:</td>
                            <td class="text-dark">{{ $supplier->bank_branch ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
