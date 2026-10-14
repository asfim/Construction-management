@extends('layouts.app')

@section('title', "প্রকল্প বাজেট ও খরচ নিয়ন্ত্রণ — {$project->name}")

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">বাজেট কোড: {{ $budget->budget_code }}</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">প্রকল্প বাজেট ও খরচ নিয়ন্ত্রণ (Cost Control & Budget)</h4>
        <p class="text-muted small mb-0">{{ $project->name }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>প্রকল্প প্রোফাইল</span>
        </a>
    </div>
</div>

<!-- 4 Cost Control Summary Cards -->
<div class="row g-4 mb-4">
    <!-- Planned Budget -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">পরিকল্পিত মোট বাজেট (Planned)</span>
            <h4 class="fw-bold text-dark mb-0">৳ {{ number_format($totalPlanned, 2) }}</h4>
            <small class="text-muted">অভ্যন্তরীণ অনুমোদিত বাজেট</small>
        </div>
    </div>

    <!-- Committed Cost (Open POs) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">কমিটেড খরচ (PO Commitment)</span>
            <h4 class="fw-bold text-warning mb-0">৳ {{ number_format($totalCommitted, 2) }}</h4>
            <small class="text-muted">ইস্যুকৃত কার্যাদেশ ও পিও</small>
        </div>
    </div>

    <!-- Actual Expenditure -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">প্রকৃত ব্যয় (Actual Expenditure)</span>
            <h4 class="fw-bold text-danger mb-0">৳ {{ number_format($totalActual, 2) }}</h4>
            <small class="text-muted">ইনভয়েস ও বিল প্রাপ্তি</small>
        </div>
    </div>

    <!-- Cost Variance -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">বাজেট ভ্যারিয়েন্স (Cost Variance)</span>
            <h4 class="fw-bold {{ $totalVariance >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                ৳ {{ number_format($totalVariance, 2) }}
            </h4>
            <small class="text-muted">{{ $totalVariance >= 0 ? 'বাজেট সাশ্রয়ী (Under Budget)' : 'বাজেট অতিক্রম (Over Budget)' }}</small>
        </div>
    </div>
</div>

<!-- Cost Code Lines Table -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-pie-chart-fill text-primary me-2"></i> খাতওয়ারী বাজেট লাইন ও খরচ নিয়ন্ত্রণ (Cost Code Lines)
        </h6>
        @can('budget.manage')
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBudgetLineModal">
            <i class="bi bi-plus-lg me-1"></i> বাজেট খাত যোগ করুন
        </button>
        @endcan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>কস্ট কোড</th>
                        <th>খাতের নাম</th>
                        <th class="text-end">পরিকল্পিত বাজেট (৳)</th>
                        <th class="text-end">কমিটেড পিও (৳)</th>
                        <th class="text-end">প্রকৃত ব্যয় (৳)</th>
                        <th class="text-end">ভ্যারিয়েন্স (৳)</th>
                        <th class="text-end">সমাপ্তি পূর্বাভাস (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budget->lines as $line)
                    <tr>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $line->costCode?->code }}</span>
                        </td>
                        <td>
                            <strong class="text-dark small">{{ $line->costCode?->name }}</strong>
                            @if($line->costCode?->name_bn)
                            <small class="text-muted d-block">({{ $line->costCode?->name_bn }})</small>
                            @endif
                        </td>
                        <td class="text-end font-monospace fw-semibold text-dark">
                            ৳ {{ number_format($line->planned_amount, 2) }}
                        </td>
                        <td class="text-end font-monospace text-warning-emphasis">
                            ৳ {{ number_format($line->committed_amount, 2) }}
                        </td>
                        <td class="text-end font-monospace text-danger">
                            ৳ {{ number_format($line->actual_amount, 2) }}
                        </td>
                        <td class="text-end font-monospace fw-bold {{ $line->variance >= 0 ? 'text-success' : 'text-danger' }}">
                            ৳ {{ number_format($line->variance, 2) }}
                        </td>
                        <td class="text-end font-monospace text-muted">
                            ৳ {{ number_format($line->forecast_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            কোনো বাজেট খাত অন্তর্ভুক্ত করা হয়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($budget->lines->isNotEmpty())
                <tfoot class="table-light fw-bold font-monospace">
                    <tr>
                        <td colspan="2" class="text-end">সর্বমোট:</td>
                        <td class="text-end text-dark">৳ {{ number_format($totalPlanned, 2) }}</td>
                        <td class="text-end text-warning-emphasis">৳ {{ number_format($totalCommitted, 2) }}</td>
                        <td class="text-end text-danger">৳ {{ number_format($totalActual, 2) }}</td>
                        <td class="text-end {{ $totalVariance >= 0 ? 'text-success' : 'text-danger' }}">৳ {{ number_format($totalVariance, 2) }}</td>
                        <td class="text-end text-muted">৳ {{ number_format($totalForecast, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Budget Line -->
<div class="modal fade" id="addBudgetLineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('budgets.lines.store', $budget) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">বাজেট খাত ও বরাদ্দ যোগ করুন</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">কস্ট কোড খাত <span class="text-danger">*</span></label>
                        <select name="cost_code_id" class="form-select" required>
                            <option value="">খাত নির্বাচন করুন</option>
                            @foreach($costCodes as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">পরিকল্পিত বরাদ্দকৃত বাজেট (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="planned_amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">সমাপ্তি পূর্বাভাস ব্যয় (৳)</label>
                        <input type="number" step="0.01" name="forecast_amount" class="form-control" placeholder="0.00">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
