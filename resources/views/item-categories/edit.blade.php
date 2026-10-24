@extends('layouts.app')

@section('title', 'Edit Item Category — ' . $itemCategory->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Edit Item Category: {{ $itemCategory->name }}</h4>
                <p class="text-muted small mb-0">Update category details, parent hierarchy, and active status</p>
            </div>
            <a href="{{ route('item-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('item-categories.update', $itemCategory) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Category Level / Type Selection -->
                    <div class="bg-light p-3 rounded border mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Category Type / স্তর পরিবর্তন করুন <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category_type" id="type_parent" value="parent" 
                                    {{ old('category_type', $currentType) === 'parent' ? 'checked' : '' }}
                                    onchange="toggleParentDropdown(this.value)">
                                <label class="form-check-label fw-semibold text-primary" for="type_parent">
                                    <i class="bi bi-folder-check me-1"></i> Main / Parent Category (প্রধান ক্যাটাগরি)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category_type" id="type_sub" value="sub" 
                                    {{ old('category_type', $currentType) === 'sub' ? 'checked' : '' }}
                                    onchange="toggleParentDropdown(this.value)">
                                <label class="form-check-label fw-semibold text-success" for="type_sub">
                                    <i class="bi bi-arrow-return-right me-1"></i> Subcategory (উপ-ক্যাটাগরি)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <!-- Parent Category Select (Shown only when Subcategory is selected) -->
                        <div class="col-12" id="parentCategoryWrapper" style="{{ old('category_type', $currentType) === 'sub' ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold">
                                Select Parent Category / মূল ক্যাটাগরি <span class="text-danger">*</span>
                            </label>
                            <select name="parent_id" id="parentIdSelect" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">-- Choose Parent Category --</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $itemCategory->parent_id) == $parent->id ? 'selected' : '' }}>
                                        📁 {{ $parent->name }} ({{ $parent->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">এই সাবক্যাটাগরিটি নির্বাচিত মূল ক্যাটাগরির অধীনে থাকবে।</div>
                            @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control font-monospace @error('code') is-invalid @enderror" 
                                value="{{ old('code', $itemCategory->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $itemCategory->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Category Name (বাংলায়)</label>
                            <input type="text" name="name_bn" class="form-control @error('name_bn') is-invalid @enderror" 
                                placeholder="যেমন: রড ও স্টিল সামগ্রী" value="{{ old('name_bn', $itemCategory->name_bn) }}">
                            @error('name_bn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description / বিবরণ</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $itemCategory->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" 
                                    {{ old('is_active', $itemCategory->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">Active in System</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('item-categories.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2-circle me-1"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleParentDropdown(type) {
    const wrapper = document.getElementById('parentCategoryWrapper');
    const select = document.getElementById('parentIdSelect');
    if (type === 'sub') {
        wrapper.style.display = 'block';
        select.setAttribute('required', 'required');
    } else {
        wrapper.style.display = 'none';
        select.removeAttribute('required');
        select.value = '';
    }
}
</script>
@endpush
@endsection
