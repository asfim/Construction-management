@props([
    'company' => null,
    'title' => '',
    'subtitle' => null,
    'docNumber' => null,
    'docNumberLabel' => 'Doc No',
    'docDate' => null,
    'docDateLabel' => 'Date',
    'status' => null,
    'statusClass' => 'bg-secondary',
    'project' => null,
    'extraMeta' => [],
])

@php
    $comp = $company ?? (isset($currentCompany) ? $currentCompany : (auth()->user()?->currentCompany ?? \App\Models\Company::first()));
@endphp

<div class="print-document-header border-bottom pb-4 mb-4">
    <div class="row align-items-center g-3">
        <!-- Left: Company Logo, Official Name & Contact Credentials -->
        <div class="col-12 col-md-7 col-print-7">
            <div class="d-flex align-items-start gap-3">
                @if($comp?->logo_url)
                    <div class="print-logo-wrapper p-1 rounded bg-white border shadow-xs" style="background:#fff;">
                        <img src="{{ $comp->logo_url }}" alt="{{ $comp->name }}" class="print-company-logo" style="max-height: 72px; max-width: 150px; object-fit: contain; display: block;">
                    </div>
                @else
                    <div class="print-logo-fallback d-flex align-items-center justify-content-center text-white fw-bold rounded shadow-xs" style="width: 58px; height: 58px; background: linear-gradient(135deg, #1e3a8a, #0284c7); font-size: 1.4rem; flex-shrink: 0;">
                        <i class="bi bi-buildings"></i>
                    </div>
                @endif
                <div>
                    <h3 class="fw-bold mb-0 text-dark print-company-name" style="font-size: 1.35rem; line-height: 1.25; letter-spacing: -0.02em;">
                        {{ $comp?->name ?? 'Construction Management Ltd.' }}
                    </h3>
                    @if($comp?->legal_name && $comp->legal_name !== $comp->name)
                        <div class="text-muted small fw-medium print-legal-name" style="font-size: 0.8rem;">
                            {{ $comp->legal_name }}
                        </div>
                    @endif

                    <div class="print-contact-info text-secondary mt-1" style="font-size: 0.8rem; line-height: 1.45;">
                        @if($comp?->address)
                            <div class="print-address">
                                <i class="bi bi-geo-alt-fill text-primary me-1 print-hide-icon"></i>
                                <span>{{ $comp->address }}@if($comp->city), {{ $comp->city }}@endif @if($comp->country), {{ $comp->country }}@endif</span>
                            </div>
                        @endif
                        <div class="d-flex flex-wrap gap-2 text-muted print-phones">
                            @if($comp?->phone)
                                <span><strong>Tel / Mobile:</strong> {{ $comp->phone }}</span>
                            @endif
                            @if($comp?->email)
                                <span>| <strong>Email:</strong> {{ $comp->email }}</span>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap gap-2 text-muted print-tax-ids">
                            @if($comp?->bin)
                                <span><strong>VAT BIN:</strong> <span class="font-monospace text-dark">{{ $comp->bin }}</span></span>
                            @endif
                            @if($comp?->tin)
                                <span>| <strong>e-TIN:</strong> <span class="font-monospace text-dark">{{ $comp->tin }}</span></span>
                            @endif
                            @if($comp?->trade_license)
                                <span>| <strong>Lic:</strong> <span class="font-monospace text-dark">{{ $comp->trade_license }}</span></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Official Document Title, Serial, Date & Status Badges -->
        <div class="col-12 col-md-5 col-print-5 text-md-end text-print-end">
            <div class="d-inline-block text-start text-md-end text-print-end w-100">
                <h4 class="fw-bold text-uppercase tracking-wider mb-1 print-title text-primary" style="letter-spacing: 0.06em; font-size: 1.25rem;">
                    {{ $title }}
                </h4>
                @if($subtitle)
                    <div class="text-muted small mb-2 fst-italic">{{ $subtitle }}</div>
                @endif

                <div class="bg-light p-2 rounded border print-meta-card d-inline-block text-start shadow-xs mt-1" style="min-width: 220px; font-size: 0.8rem;">
                    @if($docNumber)
                    <div class="d-flex justify-content-between gap-3 mb-1">
                        <span class="text-muted fw-semibold">{{ $docNumberLabel }}:</span>
                        <strong class="font-monospace text-dark">{{ $docNumber }}</strong>
                    </div>
                    @endif

                    @if($docDate)
                    <div class="d-flex justify-content-between gap-3 mb-1">
                        <span class="text-muted fw-semibold">{{ $docDateLabel }}:</span>
                        <strong class="text-dark">{{ $docDate }}</strong>
                    </div>
                    @endif

                    @if($project)
                    <div class="d-flex justify-content-between gap-3 mb-1">
                        <span class="text-muted fw-semibold">Project:</span>
                        <span class="text-dark fw-bold text-truncate" style="max-width: 145px;" title="{{ is_string($project) ? $project : $project->name }}">
                            {{ is_string($project) ? $project : ($project->code ? $project->code . ' - ' . $project->name : $project->name) }}
                        </span>
                    </div>
                    @endif

                    @foreach($extraMeta as $metaLabel => $metaVal)
                        @if($metaVal)
                        <div class="d-flex justify-content-between gap-3 mb-1">
                            <span class="text-muted fw-semibold">{{ $metaLabel }}:</span>
                            <span class="text-dark fw-semibold">{{ $metaVal }}</span>
                        </div>
                        @endif
                    @endforeach

                    @if($status)
                    <div class="d-flex justify-content-between align-items-center gap-3 mt-1 pt-1 border-top">
                        <span class="text-muted fw-semibold">Status:</span>
                        <span class="badge {{ $statusClass }} text-uppercase px-2 py-1 print-status-badge" style="font-size: 0.7rem; font-weight: 700;">
                            {{ $status }}
                        </span>
                    </div>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
