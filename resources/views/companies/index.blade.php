@extends('layouts.app')

@section('title', __('erp.company.title'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.company.title') }}</h4>
        <p class="text-muted small mb-0">মাল্টি-কোম্পানি ব্যবস্থাপনা ও কর আইনি তথ্য তালিকা</p>
    </div>
    @can('company.manage')
    <a href="{{ route('companies.create') }}" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-plus-lg"></i>
        <span>নতুন কোম্পানি যোগ করুন</span>
    </a>
    @endcan
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>কোড</th>
                        <th>কোম্পানির নাম</th>
                        <th>আইনি বিবরণ (BIN/TIN)</th>
                        <th>শাখা সংখ্যা</th>
                        <th>প্রকল্প সংখ্যা</th>
                        <th>মুদ্রা</th>
                        <th>অবস্থা</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $company->code }}</span>
                        </td>
                        <td>
                            <a href="{{ route('companies.show', $company) }}" class="fw-bold text-dark text-decoration-none d-block">
                                {{ $company->name }}
                            </a>
                            <small class="text-muted">{{ $company->legal_name }}</small>
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>BIN:</strong> {{ $company->bin ?? '—' }}</small>
                            <small class="d-block text-muted"><strong>TIN:</strong> {{ $company->tin ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $company->branches_count }} টি শাখা</span>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">{{ $company->projects_count }} টি প্রকল্প</span>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $company->currency }} ({{ $company->currency_symbol }})</span>
                        </td>
                        <td>
                            @if($company->is_active)
                            <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">নিষ্ক্রিয়</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('companies.show', $company) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> বিস্তারিত
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            কোনো কোম্পানি পাওয়া যায়নি।
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
