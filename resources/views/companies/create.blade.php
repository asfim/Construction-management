@extends('layouts.app')

@section('title', 'নতুন কোম্পানি নিবন্ধন')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">নতুন কোম্পানি নিবন্ধন</h4>
        <p class="text-muted small mb-0">নতুন মাল্টি-কোম্পানি টেন্যান্ট ও আইনি কর প্রোফাইল যোগ করুন</p>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

<div class="card card-custom border-0 shadow-sm col-12 col-lg-8 mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('companies.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">কোম্পানি কোড <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="ABC-CON" value="{{ old('code') }}" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small">কোম্পানির নাম (ইংরেজি) <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="ABC Construction Ltd." value="{{ old('name') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">আইনি নাম (বাংলা)</label>
                    <input type="text" name="legal_name" class="form-control" placeholder="এবিসি কনস্ট্রাকশন লিমিটেড" value="{{ old('legal_name') }}">
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
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">ফোন নম্বর</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">ইমেইল</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">কার্যালয়ের ঠিকানা</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">মুদ্রা (Currency)</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency', 'BDT') }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">মুদ্রা প্রতীক</label>
                    <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', '৳') }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">অর্থবছর শুরু</label>
                    <input type="text" name="fiscal_year_start" class="form-control" value="{{ old('fiscal_year_start', '07-01') }}" required>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> কোম্পানি সংরক্ষণ করুন
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
