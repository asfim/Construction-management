@extends('layouts.app')

@section('title', 'নতুন মালামাল নিবন্ধন')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">নতুন মালামাল নিবন্ধন (New Item)</h4>
        <p class="text-muted small mb-0">কনস্ট্রাকশন মালামাল ও উপকরণ ক্যাটালগ এন্ট্রি</p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

<div class="card card-custom border-0 shadow-sm col-12 col-lg-8 mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">আইটেম কোড <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="MAT-ROD-01" value="{{ old('code') }}" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small">মালামালের নাম (ইংরেজি) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="60-Grade Deformed Rebar 16mm" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">বাংলা নাম</label>
                    <input type="text" name="name_bn" class="form-control" placeholder="১৬ মিমি ৬০-গ্রেড রড" value="{{ old('name_bn') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">ক্যাটাগরি</label>
                    <select name="category_id" class="form-select">
                        <option value="">ক্যাটাগরি নির্বাচন করুন</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->name_bn }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">পরিমাপের একক (Unit) <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select" required>
                        <option value="">একক নির্বাচন করুন</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->symbol }} - {{ $unit->name_bn }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">ব্র্যান্ড (Brand)</label>
                    <input type="text" name="brand" class="form-control" placeholder="BSRM, Seven Rings, etc." value="{{ old('brand') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">কারিগরি স্পেসিফিকেশন (Specification)</label>
                    <input type="text" name="specification" class="form-control" placeholder="500W Grade, BDS EN 197-1 CEM I 42.5N, etc." value="{{ old('specification') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">স্ট্যান্ডার্ড প্রকিউরমেন্ট রেট (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">৳</span>
                        <input type="number" step="0.01" name="standard_rate" class="form-control" placeholder="0.00" value="{{ old('standard_rate', '0.00') }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">মিনিমাম অ্যালার্ট স্টক (Reorder Level)</label>
                    <input type="number" step="0.0001" name="min_stock_alert" class="form-control" placeholder="0.0000" value="{{ old('min_stock_alert', '0.0000') }}">
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> মালামাল সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
