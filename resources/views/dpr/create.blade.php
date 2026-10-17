@extends('layouts.app')

@section('title', 'New Daily Progress Report (DPR)')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">New Daily Progress Report (DPR)</h4>
        <p class="text-muted small mb-0">Record site work progress, workforce headcount, and site activity details</p>
    </div>
    <a href="{{ route('dpr.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i>
        <span>Back to List</span>
    </a>
</div>

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please correct the following errors:</strong>
    <ul class="mb-0 small ps-3">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('dpr.store') }}" method="POST" id="dprForm">
    @csrf
    <div class="row g-4">
        <!-- Main Form Area -->
        <div class="col-12 col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-clipboard-data text-primary me-2"></i> Report Overview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Project <span class="text-danger">*</span></label>
                            <select name="project_id" id="projectSelect" class="form-select" required onchange="onProjectChange(this.value)">
                                @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} ({{ $p->code }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold small">Report Date <span class="text-danger">*</span></label>
                            <input type="date" name="report_date" class="form-control" value="{{ old('report_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold small">Weather <span class="text-danger">*</span></label>
                            <select name="weather" class="form-select" required>
                                <option value="Sunny" {{ old('weather') == 'Sunny' ? 'selected' : '' }}>Sunny</option>
                                <option value="Cloudy" {{ old('weather') == 'Cloudy' ? 'selected' : '' }}>Cloudy</option>
                                <option value="Light Rain" {{ old('weather') == 'Light Rain' ? 'selected' : '' }}>Light Rain</option>
                                <option value="Heavy Rain / Storm" {{ old('weather') == 'Heavy Rain / Storm' ? 'selected' : '' }}>Heavy Rain / Storm</option>
                                <option value="Foggy" {{ old('weather') == 'Foggy' ? 'selected' : '' }}>Foggy</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Work Executed Summary <span class="text-danger">*</span></label>
                            <textarea name="work_performed" class="form-control" rows="3" placeholder="Summary of civil, structural, and finishing work performed today..." required>{{ old('work_performed') }}</textarea>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Equipment Deployed on Site</label>
                            <input type="text" name="equipment_deployed" class="form-control" placeholder="e.g. 2 Concrete Mixers, 1 Tower Crane, 3 Vibrators" value="{{ old('equipment_deployed') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small">Materials Received / Consumed</label>
                            <input type="text" name="material_received_used" class="form-control" placeholder="e.g. 150 bags OPC cement used for 4th floor columns" value="{{ old('material_received_used') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Safety Incidents, Blockers & Site Instructions</label>
                            <textarea name="issues_safety_notes" class="form-control" rows="2" placeholder="Note any HSE observations, delays, rain stoppages, or consultant queries...">{{ old('issues_safety_notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Items Table -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-list-task text-success me-2"></i> Daily Measured Work Items
                        </h6>
                        <small class="text-muted">Record physical execution quantities measured on site today</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1 shadow-sm" id="addActivityBtn">
                        <i class="bi bi-plus-circle"></i>
                        <span>Add Work Item</span>
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="activitiesTable">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th style="min-width: 240px;">Activity Description / BOQ Item <span class="text-danger">*</span></th>
                                    <th style="min-width: 140px;">Location / Grid</th>
                                    <th style="width: 130px;">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 100px;">Unit <span class="text-danger">*</span></th>
                                    <th style="min-width: 130px;">Remarks</th>
                                    <th style="width: 50px;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="activitiesTableBody">
                                @php
                                    $existingActivities = old('activities', [
                                        ['activity_name' => '', 'location' => '', 'quantity_completed' => '', 'unit' => '', 'remarks' => '', 'boq_item_id' => '']
                                    ]);
                                    if (empty($existingActivities)) {
                                        $existingActivities = [['activity_name' => '', 'location' => '', 'quantity_completed' => '', 'unit' => '', 'remarks' => '', 'boq_item_id' => '']];
                                    }
                                @endphp
                                @foreach($existingActivities as $index => $item)
                                <tr class="activity-row">
                                    <td>
                                        <input type="text" list="boqItemsList" name="activities[{{ $index }}][activity_name]"
                                               class="form-control form-control-sm activity-name-input"
                                               placeholder="Type activity or select BOQ item..."
                                               value="{{ $item['activity_name'] ?? '' }}"
                                               oninput="handleActivityInput(this)">
                                        <input type="hidden" name="activities[{{ $index }}][boq_item_id]" class="activity-boq-id" value="{{ $item['boq_item_id'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $index }}][location]" class="form-control form-control-sm"
                                               placeholder="e.g. Grid B3-C4, 5th Floor"
                                               value="{{ $item['location'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" min="0" name="activities[{{ $index }}][quantity_completed]"
                                               class="form-control form-control-sm text-end"
                                               placeholder="0.0000"
                                               value="{{ $item['quantity_completed'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $index }}][unit]"
                                               class="form-control form-control-sm activity-unit-input"
                                               placeholder="cft / sft / ton"
                                               value="{{ $item['unit'] ?? '' }}">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $index }}][remarks]" class="form-control form-control-sm"
                                               placeholder="Optional notes"
                                               value="{{ $item['remarks'] ?? '' }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-activity-row" title="Remove row" onclick="removeActivityRow(this)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Type custom site activities or select from BOQ items to auto-populate unit.
                    </small>
                    <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="addActivityBtnFooter">
                        <i class="bi bi-plus-lg me-1"></i> Add Another Work Item
                    </button>
                </div>
            </div>

            <!-- BOQ Items Datalist for Quick Autocomplete -->
            <datalist id="boqItemsList">
                @foreach($boqItems as $bi)
                <option value="{{ $bi->description }}" data-id="{{ $bi->id }}" data-unit="{{ $bi->unit }}">
                    {{ $bi->item_code }} — {{ $bi->description }} ({{ $bi->unit }})
                </option>
                @endforeach
            </datalist>
        </div>

        <!-- Right Side: Workforce Headcount & Submit -->
        <div class="col-12 col-lg-4">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-people text-warning me-2"></i> Workforce Headcount
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Skilled Workers (Masons, Carpenters, Welders) <span class="text-danger">*</span></label>
                        <input type="number" name="skilled_workers" class="form-control" value="{{ old('skilled_workers', 12) }}" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Unskilled Helpers & General Labor <span class="text-danger">*</span></label>
                        <input type="number" name="unskilled_workers" class="form-control" value="{{ old('unskilled_workers', 24) }}" min="0" required>
                    </div>
                    <div class="p-3 bg-light rounded-3 text-muted small">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        The Site Engineer submits the report and the Project Manager reviews and approves it.
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="action" value="submit" class="btn btn-primary py-2 fw-semibold shadow-sm">
                    <i class="bi bi-send me-1"></i> Submit Daily Progress Report
                </button>
                @can('dpr.approve')
                <button type="submit" name="action" value="approve" class="btn btn-success py-2 fw-semibold shadow-sm">
                    <i class="bi bi-check2-circle me-1"></i> Submit & Approve Immediately
                </button>
                @endcan
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let activityRowIndex = {{ count($existingActivities) }};

function addActivityRow() {
    const tbody = document.getElementById('activitiesTableBody');
    const tr = document.createElement('tr');
    tr.className = 'activity-row';
    tr.innerHTML = `
        <td>
            <input type="text" list="boqItemsList" name="activities[${activityRowIndex}][activity_name]"
                   class="form-control form-control-sm activity-name-input"
                   placeholder="Type activity or select BOQ item..."
                   oninput="handleActivityInput(this)">
            <input type="hidden" name="activities[${activityRowIndex}][boq_item_id]" class="activity-boq-id">
        </td>
        <td>
            <input type="text" name="activities[${activityRowIndex}][location]" class="form-control form-control-sm"
                   placeholder="e.g. Grid B3-C4, 5th Floor">
        </td>
        <td>
            <input type="number" step="0.0001" min="0" name="activities[${activityRowIndex}][quantity_completed]"
                   class="form-control form-control-sm text-end"
                   placeholder="0.0000">
        </td>
        <td>
            <input type="text" name="activities[${activityRowIndex}][unit]"
                   class="form-control form-control-sm activity-unit-input"
                   placeholder="cft / sft / ton">
        </td>
        <td>
            <input type="text" name="activities[${activityRowIndex}][remarks]" class="form-control form-control-sm"
                   placeholder="Optional notes">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-activity-row" title="Remove row" onclick="removeActivityRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    activityRowIndex++;
    tr.querySelector('.activity-name-input')?.focus();
}

function removeActivityRow(btn) {
    const row = btn.closest('tr');
    const tbody = document.getElementById('activitiesTableBody');
    if (tbody.querySelectorAll('.activity-row').length > 1) {
        row.remove();
    } else {
        row.querySelectorAll('input').forEach(input => input.value = '');
    }
}

function handleActivityInput(input) {
    const row = input.closest('tr');
    const val = input.value;
    const datalist = document.getElementById('boqItemsList');
    if (!datalist) return;

    const options = Array.from(datalist.options);
    const matched = options.find(opt => opt.value === val);

    if (matched) {
        const unitInput = row.querySelector('.activity-unit-input');
        const boqIdInput = row.querySelector('.activity-boq-id');
        if (unitInput && matched.dataset.unit) {
            unitInput.value = matched.dataset.unit;
        }
        if (boqIdInput && matched.dataset.id) {
            boqIdInput.value = matched.dataset.id;
        }
    }
}

function onProjectChange(projectId) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('project_id', projectId);
    window.location.href = currentUrl.toString();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('addActivityBtn')?.addEventListener('click', addActivityRow);
    document.getElementById('addActivityBtnFooter')?.addEventListener('click', addActivityRow);
});
</script>
@endpush
