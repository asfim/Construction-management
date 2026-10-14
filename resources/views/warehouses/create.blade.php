@extends('layouts.app')

@section('title', 'Add Warehouse / Store')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">Add Warehouse / Site Store</h4>
                <p class="text-muted small mb-0">Configure central yards or project site stores for stock receipts and issues</p>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('warehouses.store') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warehouse Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" placeholder="e.g. WH-DHAKA-01" value="{{ old('code') }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Tejgaon Central Yard & Warehouse" value="{{ old('name') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Associated Project (Site Store)</label>
                            <select name="project_id" class="form-select">
                                <option value="">None (Central / Available for All Projects)</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->name }} ({{ $proj->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Storekeeper</label>
                            <select name="storekeeper_id" class="form-select">
                                <option value="">Select Storekeeper</option>
                                @foreach($storekeepers as $sk)
                                    <option value="{{ $sk->id }}" {{ old('storekeeper_id') == $sk->id ? 'selected' : '' }}>
                                        {{ $sk->name }} ({{ $sk->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Location / Address</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Plot 88, Tejgaon Industrial Area, Dhaka" value="{{ old('location') }}">
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_central" id="isCentral" value="1" {{ old('is_central') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isCentral">Is Central Warehouse</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActive">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Warehouse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
