@extends('layouts.app')

@section('title', 'নতুন পরিমাপের একক যোগ করুন')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন পরিমাপের একক (Add Unit of Measurement)</h4>
                <p class="text-muted small mb-0">নতুন একক ও প্রতীক কনফিগার করুন যা আইটেম ও বিওকিউ তৈরিতে প্রদর্শিত হবে</p>
            </div>
            <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('units.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">এককের নাম (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="যেমন: Metric Ton" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">এককের নাম (বাংলা)</label>
                            <input type="text" name="name_bn" class="form-control" placeholder="যেমন: মেট্রিক টন" value="{{ old('name_bn') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">প্রতীক / সংক্ষেপ (Symbol) <span class="text-danger">*</span></label>
                            <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="যেমন: ton, bag, cft, sft" value="{{ old('symbol') }}" required>
                            @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">পরিমাপের ধরন (Type) <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="weight" {{ old('type') == 'weight' ? 'selected' : '' }}>ওজন (Weight - kg, ton)</option>
                                <option value="volume" {{ old('type') == 'volume' ? 'selected' : '' }}>আয়তন (Volume - cft, m3, liter)</option>
                                <option value="area" {{ old('type') == 'area' ? 'selected' : '' }}>ক্ষেত্রফল (Area - sft, sqm)</option>
                                <option value="length" {{ old('type') == 'length' ? 'selected' : '' }}>দৈর্ঘ্য (Length - rft, meter)</option>
                                <option value="count" {{ old('type') == 'count' ? 'selected' : '' }}>গণনা (Count - nos, packet)</option>
                                <option value="unit" {{ old('type', 'unit') == 'unit' ? 'selected' : '' }}>সাধারণ একক (Unit - bag, drum)</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('units.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> একক সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
