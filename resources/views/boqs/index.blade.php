@extends('layouts.app')

@section('title', 'বিওকিউ সংস্করণ তালিকা — ' . $project->name)

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-light text-dark border font-monospace">{{ $project->code }}</span>
            <span class="badge badge-status badge-{{ $project->status }}">{{ __('erp.status.' . $project->status) }}</span>
        </div>
        <h4 class="fw-bold mb-1 text-dark">বিওকিউ ও প্রাক্কলন সংস্করণ (BOQ & Revisions)</h4>
        <p class="text-muted small mb-0">{{ $project->name }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i>
            <span>প্রকল্প প্রোফাইলে ফিরুন</span>
        </a>
    </div>
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th>সংস্করণ নং</th>
                        <th>শিরোনাম ও বিবরণ</th>
                        <th>মোট আইটেম</th>
                        <th>মোট বিওকিউ মূল্য (BDT)</th>
                        <th>অনুমোদন ও লক অবস্থা</th>
                        <th>প্রস্তুতকারী / অনুমোদনকারী</th>
                        <th class="text-end">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boqVersions as $ver)
                    <tr>
                        <td>
                            <span class="badge bg-primary fs-6 px-3 py-2">Version {{ $ver->version_number }}</span>
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $ver->title }}</strong>
                            <small class="text-muted">{{ $ver->remarks ?? 'কোনো মন্তব্য নেই' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $ver->items_count }} টি আইটেম</span>
                        </td>
                        <td class="fw-bold fs-6 text-dark">
                            ৳ {{ number_format($ver->total_amount, 2) }}
                        </td>
                        <td>
                            @if($ver->is_locked)
                            <span class="badge bg-success-subtle text-success border border-success-subtle p-2">
                                <i class="bi bi-lock-fill me-1"></i> অনুমোদিত ও লক করা (Immutable)
                            </span>
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2">
                                <i class="bi bi-pencil me-1"></i> খসড়া (সম্পাদনাযোগ্য)
                            </span>
                            @endif
                        </td>
                        <td>
                            <small class="d-block text-muted"><strong>তৈরি:</strong> {{ $ver->creator?->name ?? 'System' }}</small>
                            @if($ver->approved_by)
                            <small class="d-block text-success"><strong>অনুমোদন:</strong> {{ $ver->approver?->name }} ({{ $ver->approved_at?->format('d-m-Y') }})</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('projects.boqs.show', [$project, $ver]) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-table me-1"></i> বিওকিউ খুলুন
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            কোনো বিওকিউ রেকর্ড পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
