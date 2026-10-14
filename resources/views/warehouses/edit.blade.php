@extends('layouts.app')

@section('title', 'গোডাউন / সাইট স্টোর সম্পাদনা')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">গোডাউন সম্পাদনা: {{ $warehouse->name }}</h4>
                <p class="text-muted small mb-0">গোডাউনের তথ্য ও দায়িত্বপ্রাপ্ত স্টোরকিপার পরিবর্তন করুন</p>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">গোডাউন কোড <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $warehouse->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">গোডাউনের নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $warehouse->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">সংযুক্ত প্রকল্প (Project / Site Store)</label>
                            <select name="project_id" class="form-select">
                                <option value="">কোনো প্রকল্প নয় (সেন্ট্রাল / সব প্রজেক্টের জন্য)</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $warehouse->project_id) == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->name }} ({{ $proj->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">দায়িত্বপ্রাপ্ত স্টোরকিপার (Storekeeper)</label>
                            <select name="storekeeper_id" class="form-select">
                                <option value="">স্টোরকিপার নির্বাচন করুন</option>
                                @foreach($storekeepers as $sk)
                                    <option value="{{ $sk->id }}" {{ old('storekeeper_id', $warehouse->storekeeper_id) == $sk->id ? 'selected' : '' }}>
                                        {{ $sk->name }} ({{ $sk->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">অবস্থান ও ঠিকানা (Location)</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $warehouse->location) }}">
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_central" id="isCentral" value="1" {{ old('is_central', $warehouse->is_central) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isCentral">এটি কেন্দ্রীয় গোডাউন (Central Warehouse)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> পরিবর্তন সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
