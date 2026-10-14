@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $company->code }}</span>
            <span class="badge bg-primary-subtle text-primary">{{ $company->currency }} ({{ $company->currency_symbol }})</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">{{ $company->name }}</h4>
        <p class="text-muted small mb-0">{{ $company->legal_name }}</p>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Legal & Contact Overview -->
    <div class="col-12 col-lg-5">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-file-earmark-check text-primary me-2"></i> Tax & Legal Compliance
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted" style="width: 45%;">Trade License No:</td>
                            <td class="fw-semibold text-dark">{{ $company->trade_license ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">e-TIN:</td>
                            <td class="fw-semibold text-dark">{{ $company->tin ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">VAT BIN:</td>
                            <td class="fw-semibold text-dark">{{ $company->bin ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Fiscal Year Start:</td>
                            <td class="text-dark">{{ $company->fiscal_year_start }} (July 1st)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone Number:</td>
                            <td class="text-dark">{{ $company->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email Address:</td>
                            <td class="text-dark">{{ $company->email ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Office Address:</td>
                            <td class="text-dark">{{ $company->address ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Branches List -->
    <div class="col-12 col-lg-7">
        <div class="card card-custom border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-diagram-3 text-success me-2"></i> Company Branches
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>Code</th>
                                <th>Branch Name</th>
                                <th>Contact</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($company->branches as $branch)
                            <tr>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace">{{ $branch->code }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $branch->name }}</div>
                                    @if($branch->is_head_office)
                                    <span class="badge bg-warning-subtle text-warning-emphasis" style="font-size: 0.7rem;">Head Office</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="d-block text-muted">{{ $branch->phone ?? '—' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $branch->city ?? 'Bangladesh' }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No branches found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
