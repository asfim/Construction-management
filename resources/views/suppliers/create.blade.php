@extends('layouts.app')

@section('title', 'Register New Supplier')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Register New Supplier (Vendor Master)</h4>
        <p class="text-muted small mb-0">Vendor profile, banking information, and VAT/Tax credentials</p>
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
                    <label class="form-label fw-semibold small">Vendor Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="SUP-001" value="{{ old('code') }}" required>
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small">Company / Vendor Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Supplier Company Name" value="{{ old('name') }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="Manager / Representative Name" value="{{ old('contact_person') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Phone Number</label>
                    <input type="text" name="phone" class="form-control" placeholder="+8801XXXXXXXXX" value="{{ old('phone') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="vendor@example.com" value="{{ old('email') }}">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">City / District</label>
                    <input type="text" name="city" class="form-control" placeholder="Dhaka, Chattogram" value="{{ old('city') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Office Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Detailed physical address..." value="{{ old('address') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Trade License No</label>
                    <input type="text" name="trade_license" class="form-control" value="{{ old('trade_license') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">e-TIN</label>
                    <input type="text" name="tin" class="form-control" value="{{ old('tin') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">VAT BIN</label>
                    <input type="text" name="bin" class="form-control" value="{{ old('bin') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="Eastern Bank, BRAC Bank" value="{{ old('bank_name') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Account Number</label>
                    <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Credit Terms (Days) <span class="text-danger">*</span></label>
                    <input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', 30) }}" required>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-end">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check2-circle me-1"></i> Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
