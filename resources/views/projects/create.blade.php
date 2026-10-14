@extends('layouts.app')

@section('title', __('erp.nav.new_project'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.new_project') }}</h4>
        <p class="text-muted small mb-0">নতুন কনস্ট্রাকশন প্রকল্প প্রোফাইল ও চুক্তি বিবরণ নিবন্ধন করুন</p>
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>{{ __('erp.actions.back') }}</span>
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> অনুগ্রহ করে নিচের ত্রুটিগুলি সংশোধন করুন:</strong>
    <ul class="mb-0 small ps-3">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('projects.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- 1. General Project Details -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-info-circle text-primary me-2"></i> ১. সাধারণ প্রকল্পের তথ্য (General Info)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">{{ __('erp.project.code') }} <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="PRJ-DHK-001" value="{{ old('code', 'PRJ-' . strtoupper(Str::random(6))) }}" required>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">{{ __('erp.project.name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="প্রকল্পের পূর্ণ নাম লিখুন" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.branch') }}</label>
                            <select name="branch_id" class="form-select">
                                <option value="">শাখা নির্বাচন করুন (ঐচ্ছিক)</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.manager') }}</label>
                            <select name="project_manager_id" class="form-select">
                                <option value="">প্রকল্প ব্যবস্থাপক নির্বাচন করুন</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('project_manager_id') == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }} ({{ $manager->designation ?? 'Manager' }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.description') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="কাজের পরিধি ও বিস্তারিত বিবরণ">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Client & Site Location -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-geo-alt text-success me-2"></i> ২. ক্লায়েন্ট ও সাইটের অবস্থান (Client & Site)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client') }}</label>
                            <input type="text" name="client_name" class="form-control" placeholder="ক্লায়েন্ট বা সংস্থার নাম" value="{{ old('client_name') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_contact') }}</label>
                            <input type="text" name="client_contact_person" class="form-control" placeholder="দায়িত্বপ্রাপ্ত ব্যক্তির নাম" value="{{ old('client_contact_person') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_phone') }}</label>
                            <input type="text" name="client_phone" class="form-control" placeholder="+8801XXXXXXXXX" value="{{ old('client_phone') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.client_email') }}</label>
                            <input type="email" name="client_email" class="form-control" placeholder="client@example.com" value="{{ old('client_email') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">{{ __('erp.project.site_address') }}</label>
                            <input type="text" name="site_address" class="form-control" placeholder="প্লট, রোড, এলাকা..." value="{{ old('site_address') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">শহর / জেলা (City/District)</label>
                            <input type="text" name="city" class="form-control" placeholder="Dhaka, Chattogram, etc." value="{{ old('city') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">বিভাগ (Division)</label>
                            <select name="division" class="form-select">
                                <option value="">বিভাগ নির্বাচন করুন</option>
                                <option value="Dhaka" {{ old('division') == 'Dhaka' ? 'selected' : '' }}>Dhaka (ঢাকা)</option>
                                <option value="Chattogram" {{ old('division') == 'Chattogram' ? 'selected' : '' }}>Chattogram (চট্টগ্রাম)</option>
                                <option value="Rajshahi" {{ old('division') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi (রাজশাহী)</option>
                                <option value="Khulna" {{ old('division') == 'Khulna' ? 'selected' : '' }}>Khulna (খুলনা)</option>
                                <option value="Barishal" {{ old('division') == 'Barishal' ? 'selected' : '' }}>Barishal (বরিশাল)</option>
                                <option value="Sylhet" {{ old('division') == 'Sylhet' ? 'selected' : '' }}>Sylhet (সিলেট)</option>
                                <option value="Rangpur" {{ old('division') == 'Rangpur' ? 'selected' : '' }}>Rangpur (রংপুর)</option>
                                <option value="Mymensingh" {{ old('division') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh (ময়মনসিংহ)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Financial, Contract & Timeline Side Panel -->
        <div class="col-12 col-lg-4">
            <!-- Financial Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-cash-coin text-warning me-2"></i> ৩. আর্থিক বিবরণ (Financials)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.contract_value') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="contract_value" class="form-control" placeholder="0.00" value="{{ old('contract_value', '0.00') }}" required>
                        </div>
                        <small class="text-muted">চুক্তির মোট মূল্য (BDT)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.budget_amount') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold">৳</span>
                            <input type="number" step="0.01" name="budget_amount" class="form-control" placeholder="0.00" value="{{ old('budget_amount', '0.00') }}" required>
                        </div>
                        <small class="text-muted">অনুমোদিত সর্বোচ্চ অভ্যন্তরীণ খরচ</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.retention') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="retention_percentage" class="form-control" value="{{ old('retention_percentage', '5.0') }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">{{ __('erp.project.advance_recovery') }}</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="advance_recovery_percentage" class="form-control" value="{{ old('advance_recovery_percentage', '10.0') }}">
                                <span class="input-group-text bg-light">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline & Status Card -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-calendar-range text-info me-2"></i> ৪. সময়সূচি ও অবস্থা (Timeline)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.start_date') }}</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">{{ __('erp.project.end_date') }}</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">প্রাথমিক স্ট্যাটাস <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="planning" {{ old('status', 'planning') == 'planning' ? 'selected' : '' }}>{{ __('erp.status.planning') }}</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('erp.status.active') }}</option>
                            <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>{{ __('erp.status.on_hold') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Submission Button -->
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> {{ __('erp.actions.save') }}
            </button>
        </div>
    </div>
</form>
@endsection
