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

<form action="{{ route('dpr.store') }}" method="POST">
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
                            <select name="project_id" class="form-select" required>
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
                                <option value="Sunny">Sunny</option>
                                <option value="Cloudy">Cloudy</option>
                                <option value="Light Rain">Light Rain</option>
                                <option value="Heavy Rain / Storm">Heavy Rain / Storm</option>
                                <option value="Foggy">Foggy</option>
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
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-list-task text-success me-2"></i> Daily Measured Work Items
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light small text-muted text-uppercase">
                                <tr>
                                    <th>Activity Description <span class="text-danger">*</span></th>
                                    <th>Location / Grid</th>
                                    <th style="width: 140px;">Quantity <span class="text-danger">*</span></th>
                                    <th style="width: 110px;">Unit <span class="text-danger">*</span></th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody id="activitiesTableBody">
                                @for($i = 0; $i < 3; $i++)
                                <tr>
                                    <td>
                                        <input type="text" name="activities[{{ $i }}][activity_name]" class="form-control form-control-sm" placeholder="e.g. RCC Column Casting">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $i }}][location]" class="form-control form-control-sm" placeholder="Grid B3-C4, 5th Floor">
                                    </td>
                                    <td>
                                        <input type="number" step="0.0001" name="activities[{{ $i }}][quantity_completed]" class="form-control form-control-sm text-end" placeholder="0.0000">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $i }}][unit]" class="form-control form-control-sm" placeholder="cft / sft">
                                    </td>
                                    <td>
                                        <input type="text" name="activities[{{ $i }}][remarks]" class="form-control form-control-sm" placeholder="Optional notes">
                                    </td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Submit Daily Progress Report
            </button>
        </div>
    </div>
</form>
@endsection
