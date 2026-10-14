@extends('layouts.app')

@section('title', 'Add Unit of Measurement')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Add Unit of Measurement</h4>
                <p class="text-muted small mb-0">Configure new measurement unit and symbol for materials and BOQ items</p>
            </div>
            <a href="{{ route('units.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('units.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Unit Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Metric Ton" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Symbol / Abbreviation <span class="text-danger">*</span></label>
                            <input type="text" name="symbol" class="form-control @error('symbol') is-invalid @enderror" placeholder="e.g. ton, bag, cft, sft" value="{{ old('symbol') }}" required>
                            @error('symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Measurement Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="weight" {{ old('type') == 'weight' ? 'selected' : '' }}>Weight (kg, ton)</option>
                                <option value="volume" {{ old('type') == 'volume' ? 'selected' : '' }}>Volume (cft, m3, liter)</option>
                                <option value="area" {{ old('type') == 'area' ? 'selected' : '' }}>Area (sft, sqm)</option>
                                <option value="length" {{ old('type') == 'length' ? 'selected' : '' }}>Length (rft, meter)</option>
                                <option value="count" {{ old('type') == 'count' ? 'selected' : '' }}>Count (nos, packet)</option>
                                <option value="unit" {{ old('type', 'unit') == 'unit' ? 'selected' : '' }}>General Unit (bag, drum)</option>
                            </select>
                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('units.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
