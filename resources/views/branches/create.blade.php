@extends('layouts.app')

@section('title', 'নতুন শাখা যোগ করুন')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন শাখা যোগ করুন (Add New Branch)</h4>
                <p class="text-muted small mb-0">নতুন প্রজেক্ট খোলার সময় ড্রপডাউনে বেছে নেয়ার জন্য কোম্পানির শাখা তৈরি করুন</p>
            </div>
            <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('branches.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শাখা কোড <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="যেমন: BR-DHK-01" value="{{ old('code') }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শাখার নাম <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="যেমন: ঢাকা হেড অফিস অথবা গুলশান শাখা" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ফোন নম্বর</label>
                            <input type="text" name="phone" class="form-control" placeholder="+8801700000000" value="{{ old('phone') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ইমেইল ঠিকানা</label>
                            <input type="email" name="email" class="form-control" placeholder="branch@htech.com.bd" value="{{ old('email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">শহর (City)</label>
                            <input type="text" name="city" class="form-control" placeholder="যেমন: ঢাকা" value="{{ old('city') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">বিভাগ (Division)</label>
                            <select name="division" class="form-select">
                                <option value="Dhaka" {{ old('division') == 'Dhaka' ? 'selected' : '' }}>ঢাকা (Dhaka)</option>
                                <option value="Chattogram" {{ old('division') == 'Chattogram' ? 'selected' : '' }}>চট্টগ্রাম (Chattogram)</option>
                                <option value="Sylhet" {{ old('division') == 'Sylhet' ? 'selected' : '' }}>সিলেট (Sylhet)</option>
                                <option value="Rajshahi" {{ old('division') == 'Rajshahi' ? 'selected' : '' }}>রাজশাহী (Rajshahi)</option>
                                <option value="Khulna" {{ old('division') == 'Khulna' ? 'selected' : '' }}>খুলনা (Khulna)</option>
                                <option value="Barishal" {{ old('division') == 'Barishal' ? 'selected' : '' }}>বরিশাল (Barishal)</option>
                                <option value="Rangpur" {{ old('division') == 'Rangpur' ? 'selected' : '' }}>রংপুর (Rangpur)</option>
                                <option value="Mymensingh" {{ old('division') == 'Mymensingh' ? 'selected' : '' }}>ময়মনসিংহ (Mymensingh)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">ঠিকানা (Full Address)</label>
                            <textarea name="address" rows="2" class="form-control" placeholder="শাখার সুনির্দিষ্ট ঠিকানা">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_head_office" id="isHeadOffice" value="1" {{ old('is_head_office') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isHeadOffice">প্রধান কার্যালয় / হেড অফিস (Head Office)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('branches.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> শাখা সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
