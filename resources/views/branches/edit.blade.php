@extends('layouts.app')

@section('title', 'শাখা সম্পাদনা')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">শাখা সম্পাদনা: {{ $branch->name }}</h4>
                <p class="text-muted small mb-0">শাখার তথ্য সংশোধন করুন</p>
            </div>
            <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('branches.update', $branch) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শাখা কোড <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $branch->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শাখার নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $branch->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ফোন নম্বর</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ইমেইল ঠিকানা</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শহর (City)</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $branch->city) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">বিভাগ (Division)</label>
                            <select name="division" class="form-select">
                                <option value="Dhaka" {{ old('division', $branch->division) == 'Dhaka' ? 'selected' : '' }}>ঢাকা (Dhaka)</option>
                                <option value="Chattogram" {{ old('division', $branch->division) == 'Chattogram' ? 'selected' : '' }}>চট্টগ্রাম (Chattogram)</option>
                                <option value="Sylhet" {{ old('division', $branch->division) == 'Sylhet' ? 'selected' : '' }}>সিলেট (Sylhet)</option>
                                <option value="Rajshahi" {{ old('division', $branch->division) == 'Rajshahi' ? 'selected' : '' }}>রাজশাহী (Rajshahi)</option>
                                <option value="Khulna" {{ old('division', $branch->division) == 'Khulna' ? 'selected' : '' }}>খুলনা (Khulna)</option>
                                <option value="Barishal" {{ old('division', $branch->division) == 'Barishal' ? 'selected' : '' }}>বরিশাল (Barishal)</option>
                                <option value="Rangpur" {{ old('division', $branch->division) == 'Rangpur' ? 'selected' : '' }}>রংপুর (Rangpur)</option>
                                <option value="Mymensingh" {{ old('division', $branch->division) == 'Mymensingh' ? 'selected' : '' }}>ময়মনসিংহ (Mymensingh)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">ঠিকানা (Full Address)</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $branch->address) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_head_office" id="isHeadOffice" value="1" {{ old('is_head_office', $branch->is_head_office) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isHeadOffice">প্রধান কার্যালয় / হেড অফিস (Head Office)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('branches.index') }}" class="btn btn-light">বাতিল</a>
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
