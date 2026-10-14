@extends('layouts.app')

@section('title', __('erp.nav.audit_logs'))

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('erp.nav.audit_logs') }}</h4>
        <p class="text-muted small mb-0">অপরিবর্তনীয় সিস্টেম কার্যকলাপ ও পরিবর্তন অডিট ট্রেইল</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card card-custom border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <select name="action" class="form-select">
                    <option value="">সকল অ্যাকশন (All Actions)</option>
                    <option value="login" {{ $action == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ $action == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="created" {{ $action == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ $action == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ $action == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <select name="user_id" class="form-select">
                    <option value="">সকল ব্যবহারকারী (All Users)</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> ফিল্টার করুন
                </button>
                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Audit Trail Table -->
<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 font-monospace small">
                <thead class="table-light text-muted text-uppercase">
                    <tr>
                        <th style="width: 170px;">তারিখ ও সময়</th>
                        <th>ব্যবহারকারী</th>
                        <th>কার্যক্রম</th>
                        <th>এনটিটি ও আইডি</th>
                        <th>বিবরণ</th>
                        <th>আইপি ঠিকানা</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-nowrap text-secondary">
                            {{ $log->created_at->format('d-m-Y H:i:s') }}
                        </td>
                        <td>
                            <strong class="text-dark">{{ $log->user_name ?? 'System' }}</strong>
                        </td>
                        <td>
                            @php
                                $badgeClass = match($log->action) {
                                    'created' => 'bg-success-subtle text-success border border-success-subtle',
                                    'updated' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                    'deleted' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    'login' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
                                    'logout' => 'bg-secondary-subtle text-secondary border',
                                    default => 'bg-light text-dark border',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ class_basename($log->auditable_type) }}</span>
                            @if($log->auditable_id)
                            <span class="badge bg-light text-dark border">#{{ $log->auditable_id }}</span>
                            @endif
                        </td>
                        <td class="text-break font-sans-serif" style="max-width: 350px;">
                            {{ $log->description }}
                            @if(!empty($log->new_values))
                            <details class="mt-1">
                                <summary class="text-primary small" style="cursor: pointer;">পরিবর্তিত মান দেখুন</summary>
                                <pre class="bg-light p-2 rounded mt-1 mb-0" style="font-size: 0.72rem;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </details>
                            @endif
                        </td>
                        <td class="text-muted text-nowrap">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            কোনো অডিট রেকর্ড পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
