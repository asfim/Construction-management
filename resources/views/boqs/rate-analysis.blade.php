@extends('layouts.app')

@section('title', "রেট অ্যানালাইসিস — {$boqItem->item_code}")

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $boqItem->item_code }}</span>
            <span class="badge bg-primary-subtle text-primary">একক: {{ $boqItem->unit }}</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">রেট অ্যানালাইসিস (Rate Analysis Breakdown)</h4>
        <p class="text-muted small mb-0">{{ $boqItem->description }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('projects.boqs.show', [$boqItem->boqVersion->project, $boqItem->boqVersion]) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>বিওকিউ-তে ফিরুন</span>
        </a>
    </div>
</div>

<!-- Computed Rate Highlight Card -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">উপকরণ খরচ (Material)</span>
            <h4 class="fw-bold text-dark mb-0">৳ {{ number_format($rateAnalysis->material_cost, 2) }}</h4>
            <small class="text-muted">প্রতি {{ $boqItem->unit }}</small>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">শ্রমিক খরচ (Labor)</span>
            <h4 class="fw-bold text-dark mb-0">৳ {{ number_format($rateAnalysis->labor_cost, 2) }}</h4>
            <small class="text-muted">প্রতি {{ $boqItem->unit }}</small>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-semibold d-block mb-1">যন্ত্রপাতি খরচ (Plant/Equipment)</span>
            <h4 class="fw-bold text-dark mb-0">৳ {{ number_format($rateAnalysis->equipment_cost, 2) }}</h4>
            <small class="text-muted">প্রতি {{ $boqItem->unit }}</small>
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="card card-custom p-3 border-0 shadow-sm h-100 bg-primary text-white">
            <span class="text-white-50 small fw-semibold d-block mb-1">মোট গণনাকৃত দর (Total Rate)</span>
            <h3 class="fw-bold text-white mb-0">৳ {{ number_format($rateAnalysis->total_unit_rate, 2) }}</h3>
            <small class="text-white-50">ওভারহেড, প্রফিট ও অপচয় সহ</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Components Table -->
    <div class="col-12 col-lg-8">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-stack text-primary me-2"></i> কম্পোনেন্ট ও উপাদানের বিস্তারিত
                </h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addComponentModal">
                    <i class="bi bi-plus-lg me-1"></i> উপাদান যোগ করুন
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-muted text-uppercase">
                            <tr>
                                <th>ধরণ</th>
                                <th>বিবরণ</th>
                                <th>একক</th>
                                <th class="text-end">পরিমাণ</th>
                                <th class="text-end">দর (৳)</th>
                                <th class="text-end">মোট খরচ (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rateAnalysis->components as $comp)
                            <tr>
                                <td>
                                    @php
                                        $typeBadge = match($comp->component_type) {
                                            'material' => 'bg-info-subtle text-info-emphasis',
                                            'labor' => 'bg-warning-subtle text-warning-emphasis',
                                            'equipment' => 'bg-secondary-subtle text-secondary',
                                            default => 'bg-light text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $typeBadge }} text-capitalize">
                                        {{ $comp->component_type }}
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark small">{{ $comp->description }}</strong>
                                </td>
                                <td>{{ $comp->unit }}</td>
                                <td class="text-end font-monospace">{{ number_format($comp->quantity, 4) }}</td>
                                <td class="text-end font-monospace">৳ {{ number_format($comp->rate, 2) }}</td>
                                <td class="text-end font-monospace fw-bold text-dark">৳ {{ number_format($comp->amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    কোনো উপাদান যোগ করা হয়নি।
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Markup & Adjustment Parameters -->
    <div class="col-12 col-lg-4">
        <div class="card card-custom border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-percent text-success me-2"></i> অপচয়, ওভারহেড ও মুনাফা (%)
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('rate-analyses.parameters.update', $rateAnalysis) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">উপকরণ অপচয় (Material Wastage %)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="wastage_percentage" class="form-control" value="{{ old('wastage_percentage', $rateAnalysis->wastage_percentage) }}" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">কোম্পানি ওভারহেড (Overhead %)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="overhead_percentage" class="form-control" value="{{ old('overhead_percentage', $rateAnalysis->overhead_percentage) }}" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">ঠিকাদারী লাভ (Contractor's Profit %)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="profit_percentage" class="form-control" value="{{ old('profit_percentage', $rateAnalysis->profit_percentage) }}" required>
                            <span class="input-group-text bg-light">%</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-arrow-repeat me-1"></i> পুনঃগণনা ও রেট হালনাগাদ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Component -->
<div class="modal fade" id="addComponentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('rate-analyses.components.store', $rateAnalysis) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">নতুন উপাদান যোগ করুন</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">উপাদানের ধরণ <span class="text-danger">*</span></label>
                        <select name="component_type" class="form-select" required>
                            <option value="material">উপকরণ (Material)</option>
                            <option value="labor">শ্রমিক (Labor)</option>
                            <option value="equipment">যন্ত্রপাতি (Equipment)</option>
                            <option value="transport">পরিবহন (Transport)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">উপকরণের বিবরণ <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" placeholder="যেমন: রাজমিস্ত্রি, সিমেন্ট, সিলেট বালু..." required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">একক <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" placeholder="bag, cft, day" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">পরিমাণ <span class="text-danger">*</span></label>
                            <input type="number" step="0.0001" name="quantity" class="form-control" placeholder="0.0000" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">একক দর (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="rate" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">যোগ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
