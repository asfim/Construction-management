@extends('layouts.app')

@section('title', __('erp.company.title'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0 text-dark">Company Profile & Settings</h4>
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-shield-check me-1"></i> Single Company
            </span>
        </div>
        <p class="text-muted small mb-0">Official legal entity, logo, contact, tax IDs (BIN/TIN), and operational parameters</p>
    </div>
    @can('company.manage')
        @if($companies->count() < 1)
        <a href="{{ route('companies.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Add Company</span>
        </a>
        @else
        <a href="{{ route('companies.edit', $companies->first()) }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-gear-fill"></i>
            <span>Edit Company Settings</span>
        </a>
        @endif
    @endcan
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>Code</th>
                        <th>Company Name</th>
                        <th>Tax & Legal (BIN/TIN)</th>
                        <th>Branches</th>
                        <th>Projects</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $company->code }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($company->logo_url)
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="border rounded bg-white p-1" style="width: 38px; height: 38px; object-fit: contain;">
                                @else
                                    <div class="rounded d-flex align-items-center justify-content-center text-white fw-bold" style="width: 38px; height: 38px; background: linear-gradient(135deg, #1e3a8a, #0284c7); font-size: 0.85rem;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('companies.show', $company) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $company->name }}
                                    </a>
                                    <small class="text-muted">{{ $company->legal_name }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>BIN:</strong> {{ $company->bin ?? '—' }}</small>
                            <small class="d-block text-muted"><strong>TIN:</strong> {{ $company->tin ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $company->branches_count }} Branches</span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">{{ $company->projects_count }} Projects</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $company->currency }} ({{ $company->currency_symbol }})</span>
                        </td>
                        <td>
                            @if($company->is_active)
                            <span class="badge bg-success-subtle text-success">Active</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-primary" title="Details">
                                    <i class="bi bi-eye"></i> Details
                                </a>
                                @can('company.manage')
                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-outline-secondary" title="Edit Company">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            No companies found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($companies->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $companies->links() }}
    </div>
    @endif
</div>
@endsection
