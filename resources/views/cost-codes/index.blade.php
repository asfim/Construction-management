@extends('layouts.app')

@section('title', 'কস্ট কোড ও ডব্লিউবিএস কাঠামো')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">কস্ট কোড ও ডব্লিউবিএস কাঠামো (Cost Codes & WBS)</h4>
        <p class="text-muted small mb-0">কনস্ট্রাকশন কাজের খাতওয়ারী খরচ কোড ও শ্রেণিবিন্যাস</p>
    </div>
    @can('cost-code.manage')
    <button class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newCostCodeModal">
        <i class="bi bi-plus-lg"></i>
        <span>নতুন কস্ট কোড যোগ করুন</span>
    </button>
    @endcan
</div>

<div class="card card-custom border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-muted text-uppercase">
                    <tr>
                        <th style="width: 160px;">কস্ট কোড</th>
                        <th>খাতের নাম (ইংরেজি ও বাংলা)</th>
                        <th>বিবরণ</th>
                        <th>অবস্থা</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costCodes as $code)
                    <tr class="table-light">
                        <td>
                            <strong class="font-monospace text-primary fs-6">{{ $code->code }}</strong>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $code->name }}</strong>
                            @if($code->name_bn)
                            <span class="text-muted">({{ $code->name_bn }})</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $code->description ?? '—' }}</td>
                        <td>
                            <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                        </td>
                    </tr>
                        @foreach($code->children as $child)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border font-monospace ms-2">↳ {{ $child->code }}</span>
                            </td>
                            <td class="ps-4">
                                <span class="text-dark">{{ $child->name }}</span>
                                @if($child->name_bn)
                                <small class="text-muted">({{ $child->name_bn }})</small>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $child->description ?? '—' }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            কোনো কস্ট কোড পাওয়া যায়নি।
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Cost Code -->
<div class="modal fade" id="newCostCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cost-codes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">নতুন কস্ট কোড যোগ করুন</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">প্যারেন্ট কস্ট কোড (ঐচ্ছিক)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">মূল ক্যাটাগরি হিসেবে যোগ করুন</option>
                            @foreach($costCodes as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->code }} — {{ $cc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">কোড <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="01.01-EXCAVATION" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">নাম (ইংরেজি) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Excavation Work" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">নাম (বাংলা)</label>
                        <input type="text" name="name_bn" class="form-control" placeholder="মাটি খনন কাজ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">বিবরণ</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
