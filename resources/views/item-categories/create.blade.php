@extends('layouts.app')

@section('title', 'নতুন আইটেম ক্যাটাগরি তৈরি')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন আইটেম ক্যাটাগরি (Create Item Category)</h4>
                <p class="text-muted small mb-0">উপকরণ তৈরির সময় ড্রপডাউনে প্রদর্শনের জন্য নতুন ক্যাটাগরি যোগ করুন</p>
            </div>
            <a href="{{ route('item-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('item-categories.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরি কোড <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="যেমন: CAT-STEEL" value="{{ old('code') }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">মূল ক্যাটাগরি (Parent Category)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">কোনোটি নয় (Root Category)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ $parent->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরির নাম (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="যেমন: Structural Steel & Rebar" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরির নাম (বাংলা)</label>
                            <input type="text" name="name_bn" class="form-control" placeholder="যেমন: রড ও রিবার" value="{{ old('name_bn') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">বিবরণ (Description)</label>
                            <textarea name="description" rows="3" class="form-control" placeholder="ক্যাটাগরির বিস্তারিত বিবরণ...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('item-categories.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> ক্যাটাগরি সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
