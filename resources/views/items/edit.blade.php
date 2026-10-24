@extends('layouts.app')

@section('title', 'Edit Material — ' . $item->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $item->code }}</span>
            <span class="badge {{ $item->is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                {{ $item->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Edit Material: {{ $item->name }}</h4>
        <p class="text-muted small mb-0">Update construction material specifications, rates, and catalog details</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>Back to List</span>
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm col-12 col-lg-8 mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('items.update', $item) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Item Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace @error('code') is-invalid @enderror"
                        value="{{ old('code', $item->code) }}" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-8">
                    <label class="form-label fw-semibold small">Material Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $item->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Material Name (বাংলা)</label>
                    <input type="text" name="name_bn" class="form-control @error('name_bn') is-invalid @enderror"
                        value="{{ old('name_bn', $item->name_bn) }}" placeholder="যেমন: ১৬ মিমি রড, ওপিসি সিমেন্ট">
                    @error('name_bn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Unit of Measurement (UoM) <span class="text-danger">*</span></label>
                    <select name="unit_id" class="form-select @error('unit_id') is-invalid @enderror" required>
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }} ({{ $unit->symbol }})
                        </option>
                        @endforeach
                    </select>
                    @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Brand</label>
                    <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror"
                        placeholder="BSRM, Seven Rings, etc." value="{{ old('brand', $item->brand) }}">
                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Standard Procurement Rate (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">৳</span>
                        <input type="number" step="0.01" name="standard_rate" class="form-control @error('standard_rate') is-invalid @enderror"
                            placeholder="0.00" value="{{ old('standard_rate', $item->standard_rate) }}" required>
                        @error('standard_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold small">Minimum Stock Alert (Reorder Level)</label>
                    <input type="number" step="0.0001" name="min_stock_alert" class="form-control @error('min_stock_alert') is-invalid @enderror"
                        placeholder="0.0000" value="{{ old('min_stock_alert', $item->min_stock_alert) }}">
                    @error('min_stock_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small">Technical Specification</label>
                    <input type="text" name="specification" class="form-control @error('specification') is-invalid @enderror"
                        placeholder="500W Grade, BDS EN 197-1 CEM I 42.5N, etc." value="{{ old('specification', $item->specification) }}">
                    @error('specification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActiveSwitch"
                            {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small text-dark" for="isActiveSwitch">
                            Active in Catalog (Available for MR, PO & Estimation)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between">
                @can('item.manage')
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Are you sure you want to delete this material?')) document.getElementById('deleteItemForm').submit();">
                    <i class="bi bi-trash me-1"></i> Delete Material
                </button>
                @endcan
                <div class="d-flex gap-2 ms-auto">
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary px-3 py-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Update Material
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteItemForm" action="{{ route('items.destroy', $item) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
