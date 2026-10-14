@extends('layouts.app')

@section('title', 'আইটেম ক্যাটাগরি সম্পাদনা')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">আইটেম ক্যাটাগরি সম্পাদনা: {{ $itemCategory->name }}</h4>
                <p class="text-muted small mb-0">ক্যাটাগরির তথ্য সংশোধন করুন</p>
            </div>
            <a href="{{ route('item-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('item-categories.update', $itemCategory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরি কোড <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $itemCategory->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">মূল ক্যাটাগরি (Parent Category)</label>
                            <select name="parent_id" class="form-select">
                                <option value="">কোনোটি নয় (Root Category)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $itemCategory->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ $parent->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরির নাম (English) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $itemCategory->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ক্যাটাগরির নাম (বাংলা)</label>
                            <input type="text" name="name_bn" class="form-control" value="{{ old('name_bn', $itemCategory->name_bn) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">বিবরণ (Description)</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $itemCategory->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $itemCategory->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">সক্রিয় রাখুন (Active)</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('item-categories.index') }}" class="btn btn-light">বাতিল</a>
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
