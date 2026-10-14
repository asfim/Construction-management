@extends('layouts.app')

@section('title', 'নতুন সরবরাহকারী নিবন্ধন')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">নতুন সরবরাহকারী নিবন্ধন (New Vendor)</h4>
        <p class="text-muted small mb-0">ভেন্ডর প্রোফাইল, ব্যাংক তথ্য এবং ভ্যাট/ট্যাক্স নিবন্ধন</p>
    </div>
    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

<div class="card card-custom border-0 shadow-sm col-12 col-lg-8 mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('suppliers.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">ভেন্ডর কোড <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="SUP-001" value="{{ old('code') }}" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small">প্রতিষ্ঠানের নাম <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="সরবরাহকারী কোম্পানির নাম" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">দায়িত্বপ্রাপ্ত যোগাযোগ ব্যক্তি</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="ব্যবস্থাপক / প্রতিনিধির নাম" value="{{ old('contact_person') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">মোবাইল নম্বর</label>
                    <input type="text" name="phone" class="form-control" placeholder="+8801XXXXXXXXX" value="{{ old('phone') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">ইমেইল</label>
                    <input type="email" name="email" class="form-control" placeholder="vendor@example.com" value="{{ old('email') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">শহর / জেলা</label>
                    <input type="text" name="city" class="form-control" placeholder="Dhaka, Chattogram" value="{{ old('city') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">কার্যালয়ের ঠিকানা</label>
                    <input type="text" name="address" class="form-control" placeholder="বিস্তারিত ঠিকানা..." value="{{ old('address') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">ট্রেড লাইসেন্স নং</label>
                    <input type="text" name="trade_license" class="form-control" value="{{ old('trade_license') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">ই-টিন (TIN)</label>
                    <input type="text" name="tin" class="form-control" value="{{ old('tin') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">ভ্যাট বিন (BIN)</label>
                    <input type="text" name="bin" class="form-control" value="{{ old('bin') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">ব্যাংকের নাম</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="Eastern Bank, BRAC Bank" value="{{ old('bank_name') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">অ্যাকাউন্ট নম্বর</label>
                    <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">পেমেন্ট ক্রেডিট শর্ত (দিন) <span class="text-danger">*</span></label>
                    <input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', 30) }}" required>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> সরবরাহকারী সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
