@extends('layouts.app')

@section('title', 'নতুন উপকরণ চাহিদা তৈরি')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">নতুন উপকরণ চাহিদা তৈরি (Create Material Requisition)</h4>
                <p class="text-muted small mb-0">সাইটে কাজের জন্য প্রয়োজনীয় মালামালের তালিকা যুক্ত করুন</p>
            </div>
            <a href="{{ route('material-requisitions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> তালিকায় ফিরে যান
            </a>
        </div>

        <div class="card card-custom">
            <div class="card-body p-4">
                <form action="{{ route('material-requisitions.store') }}" method="POST" id="mrForm">
                    @csrf

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">প্রকল্প (Project) <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                <option value="">প্রকল্প নির্বাচন করুন</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }} ({{ $project->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">রিকুইজিশন নম্বর <span class="text-danger">*</span></label>
                            <input type="text" name="requisition_no" class="form-control @error('requisition_no') is-invalid @enderror"
                                value="{{ old('requisition_no', 'MR-' . date('Ymd') . '-' . rand(100, 999)) }}" required>
                            @error('requisition_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">দাখিলের তারিখ <span class="text-danger">*</span></label>
                            <input type="date" name="requisition_date" class="form-control @error('requisition_date') is-invalid @enderror"
                                value="{{ old('requisition_date', date('Y-m-d')) }}" required>
                            @error('requisition_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-semibold">প্রয়োজনীয়তার তারিখ</label>
                            <input type="date" name="required_by_date" class="form-control"
                                value="{{ old('required_by_date', date('Y-m-d', strtotime('+7 days'))) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">ব্যবহারের বিবরণ / উদ্দেশ্য (Notes)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="যেমন: ২য় তলার ছাদ ঢালাই ও কলামের কাজের জন্য">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3 border-bottom pb-2">উপকরণের তালিকা (Requisition Items)</h5>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="itemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">উপকরণ (Item) <span class="text-danger">*</span></th>
                                    <th style="width: 25%;">কস্ট কোড (Cost Code)</th>
                                    <th style="width: 20%;">পরিমাণ (Quantity) <span class="text-danger">*</span></th>
                                    <th style="width: 15%;">মন্তব্য (Remarks)</th>
                                    <th style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody">
                                <tr>
                                    <td>
                                        <select name="items[0][item_id]" class="form-select form-select-sm" required>
                                            <option value="">উপকরণ বেছে নিন</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }} ({{ $item->unit?->symbol ?? '' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="items[0][cost_code_id]" class="form-select form-select-sm">
                                            <option value="">কস্ট কোড (ঐচ্ছিক)</option>
                                            @foreach($costCodes as $cc)
                                                <option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0.0001" name="items[0][quantity_requested]" class="form-control form-select-sm" placeholder="পরিমাণ" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][remarks]" class="form-control form-select-sm" placeholder="মন্তব্য">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mb-4" id="addRowBtn">
                        <i class="bi bi-plus-lg me-1"></i> আরো আইটেম যোগ করুন
                    </button>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('material-requisitions.index') }}" class="btn btn-light">বাতিল</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> রিকুইজিশন দাখিল করুন (Submit MR)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let rowIndex = 1;
    const addRowBtn = document.getElementById('addRowBtn');
    const itemsBody = document.getElementById('itemsBody');

    const itemOptions = `@foreach($items as $item)<option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit?->symbol ?? '' }})</option>@endforeach`;
    const ccOptions = `@foreach($costCodes as $cc)<option value="{{ $cc->id }}">{{ $cc->code }} - {{ $cc->name }}</option>@endforeach`;

    addRowBtn.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select form-select-sm" required>
                    <option value="">উপকরণ বেছে নিন</option>
                    ${itemOptions}
                </select>
            </td>
            <td>
                <select name="items[${rowIndex}][cost_code_id]" class="form-select form-select-sm">
                    <option value="">কস্ট কোড (ঐচ্ছিক)</option>
                    ${ccOptions}
                </select>
            </td>
            <td>
                <input type="number" step="0.0001" min="0.0001" name="items[${rowIndex}][quantity_requested]" class="form-control form-select-sm" placeholder="পরিমাণ" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][remarks]" class="form-control form-select-sm" placeholder="মন্তব্য">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        itemsBody.appendChild(row);
        rowIndex++;
        updateRemoveButtons();
    });

    itemsBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = itemsBody.querySelectorAll('tr');
        rows.forEach(r => {
            const btn = r.querySelector('.remove-row');
            if (btn) btn.disabled = (rows.length === 1);
        });
    }
});
</script>
@endsection
