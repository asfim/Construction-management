@extends('layouts.app')

@section('title', 'Edit Company: ' . $company->name)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $company->code }}</span>
            <span class="badge bg-primary-subtle text-primary">{{ $company->currency }} ({{ $company->currency_symbol }})</span>
            <span class="badge bg-success-subtle text-success border border-success-subtle">
                <i class="bi bi-shield-check me-1"></i> Single Company Profile
            </span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">Edit Company Settings & Profile</h4>
        <p class="text-muted small mb-0">Update company identity, logo, legal credentials, and contact details for all official prints & invoices</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>{{ __('erp.actions.back') }}</span>
        </a>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-12 col-xl-10">
        <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Company Logo & Branding -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-image text-primary me-2"></i> Company Logo & Print Branding
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-md-4 text-center">
                            <div class="p-3 border rounded-3 bg-light d-inline-block shadow-sm text-center" style="min-width: 180px;">
                                @if($company->logo_url)
                                    <img id="logoPreview" src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="img-fluid rounded mb-2" style="max-height: 90px; max-width: 180px; object-fit: contain;">
                                    <div class="form-check mt-2 text-start small">
                                        <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                        <label class="form-check-label text-danger fw-semibold" for="removeLogoCheck">
                                            Remove current logo
                                        </label>
                                    </div>
                                @else
                                    <div id="logoPreviewPlaceholder" class="d-flex align-items-center justify-content-center text-white fw-bold rounded mx-auto mb-2" style="width: 80px; height: 80px; background: linear-gradient(135deg, #1e3a8a, #0284c7); font-size: 1.8rem;">
                                        <i class="bi bi-building"></i>
                                    </div>
                                    <span class="text-muted small d-block">No custom logo uploaded</span>
                                    <img id="logoPreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 90px; max-width: 180px; object-fit: contain;">
                                @endif
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">Upload New Logo (Image)</label>
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*" onchange="previewCompanyLogo(this)">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Recommended: Transparent PNG or high-res JPG/SVG (around 400x120px, max 2MB). This logo will appear at the header of all official Purchase Orders, Goods Receipt Notes, Client Bills (RA Bills), Expense Vouchers, and DPR prints.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Entity Information -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-buildings text-primary me-2"></i> Company Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Company Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $company->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold small">Company Display Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $company->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Legal Entity Name (Registered under Companies Act)</label>
                            <input type="text" name="legal_name" class="form-control @error('legal_name') is-invalid @enderror" value="{{ old('legal_name', $company->legal_name) }}" placeholder="e.g. H Tech Provision Construction Limited">
                            @error('legal_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Corporate Office Location -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-geo-alt text-primary me-2"></i> Official Contact & Address Details
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Corporate Office Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="e.g. House 42, Road 11, Block D, Banani">{{ old('address', $company->address) }}</textarea>
                            <div class="form-text small">This address will be printed at the top of all documents, invoices, vouchers & purchase orders.</div>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $company->city) }}" placeholder="e.g. Dhaka">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Division / State</label>
                            <input type="text" name="division" class="form-control" value="{{ old('division', $company->division) }}" placeholder="e.g. Dhaka">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? 'Bangladesh') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Phone / Mobile Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $company->phone) }}" placeholder="+880 1711-000000">
                            </div>
                            @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $company->email) }}" placeholder="info@company.com">
                            </div>
                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax & Statutory Compliance -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-shield-check text-primary me-2"></i> Tax & Legal Compliance
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">VAT BIN (13/9 Digit)</label>
                            <input type="text" name="bin" class="form-control" value="{{ old('bin', $company->bin) }}" placeholder="002819482-0101">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">e-TIN (Tax Number)</label>
                            <input type="text" name="tin" class="form-control" value="{{ old('tin', $company->tin) }}" placeholder="482910482910">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Trade License No</label>
                            <input type="text" name="trade_license" class="form-control" value="{{ old('trade_license', $company->trade_license) }}" placeholder="TRAD/DSCC/012984/2024">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Currency Code</label>
                            <input type="text" name="currency" class="form-control" value="{{ old('currency', $company->currency) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $company->currency_symbol) }}" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold small">Fiscal Year Start (MM-DD)</label>
                            <input type="text" name="fiscal_year_start" class="form-control" value="{{ old('fiscal_year_start', $company->fiscal_year_start) }}" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="isActiveSwitch" {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="isActiveSwitch">Company is Active</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-5">
                <a href="{{ route('companies.show', $company) }}" class="btn btn-outline-secondary px-4 py-2">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Update Company Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewCompanyLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            const placeholder = document.getElementById('logoPreviewPlaceholder');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            if (placeholder) {
                placeholder.classList.add('d-none');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
