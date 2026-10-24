<?php

namespace App\Http\Controllers;

use App\Models\BoqItem;
use App\Models\DailyProgressReport;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyProgressReportController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('dpr.view');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();
        $status = $request->string('status')->trim()->value();

        // Default to unapproved if no status was specified
        if (! $request->has('status') || $status === '') {
            $status = 'unapproved';
        }

        $baseQuery = DailyProgressReport::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

        $counts = [
            'unapproved' => (clone $baseQuery)->where('status', '!=', 'approved')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'all' => (clone $baseQuery)->count(),
        ];

        $reports = (clone $baseQuery)
            ->when($status === 'unapproved', fn ($q) => $q->where('status', '!=', 'approved'))
            ->when($status === 'submitted', fn ($q) => $q->where('status', 'submitted'))
            ->when($status === 'approved', fn ($q) => $q->where('status', 'approved'))
            ->when(! in_array($status, ['unapproved', 'submitted', 'approved', 'all']), fn ($q) => $q->where('status', $status))
            ->with(['project', 'siteEngineer', 'approver', 'activities'])
            ->latest('report_date')
            ->paginate(15)
            ->withQueryString();

        return view('dpr.index', compact('reports', 'status', 'counts'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('dpr.create');

        $companyId = $tenantManager->currentCompanyId();
        $activeProjectId = $tenantManager->currentProjectId();

        $projects = Project::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $selectedProjectId = $request->integer('project_id') ?: $activeProjectId ?: $projects->first()?->id;

        $boqItems = collect();
        if ($selectedProjectId) {
            $boqItems = BoqItem::whereHas('boqVersion', function ($q) use ($selectedProjectId) {
                $q->where('project_id', $selectedProjectId);
            })->get();
        }

        return view('dpr.create', compact('projects', 'selectedProjectId', 'boqItems'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('dpr.create');

        // Pre-process and filter activities: remove completely empty rows
        if ($request->has('activities') && is_array($request->activities)) {
            $filtered = [];
            foreach ($request->activities as $act) {
                if (!is_array($act)) continue;
                $name = trim($act['activity_name'] ?? '');
                $qty = trim((string)($act['quantity_completed'] ?? ''));
                if ($name !== '' || $qty !== '') {
                    $act['activity_name'] = $name;
                    $act['boq_item_id'] = !empty($act['boq_item_id']) ? (int) $act['boq_item_id'] : null;
                    $filtered[] = $act;
                }
            }
            $request->merge(['activities' => $filtered]);
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'report_date' => 'required|date',
            'weather' => 'required|string|max:50',
            'skilled_workers' => 'required|integer|min:0',
            'unskilled_workers' => 'required|integer|min:0',
            'work_performed' => 'required|string',
            'equipment_deployed' => 'nullable|string',
            'material_received_used' => 'nullable|string',
            'issues_safety_notes' => 'nullable|string',
            'activities' => 'nullable|array',
            'activities.*.activity_name' => 'required|string|max:255',
            'activities.*.location' => 'nullable|string|max:255',
            'activities.*.quantity_completed' => 'required|numeric|min:0',
            'activities.*.unit' => 'required|string|max:20',
            'activities.*.boq_item_id' => 'nullable|exists:boq_items,id',
            'activities.*.remarks' => 'nullable|string|max:255',
        ]);

        $companyId = $tenantManager->currentCompanyId();
        $dateStr = str_replace('-', '', $validated['report_date']);

        // Inspect existing DPR numbers company-wide without ProjectScope filtering
        $existingNumbers = DailyProgressReport::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('dpr_number', 'LIKE', "DPR-{$dateStr}-%")
            ->pluck('dpr_number')
            ->toArray();

        $maxSeq = 0;
        foreach ($existingNumbers as $num) {
            if (preg_match('/DPR-\d+-(\d+)/', $num, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxSeq) {
                    $maxSeq = $seq;
                }
            }
        }

        $nextSeq = $maxSeq + 1;
        $dprNumber = sprintf('DPR-%s-%03d', $dateStr, $nextSeq);

        while (DailyProgressReport::withoutGlobalScopes()->where('company_id', $companyId)->where('dpr_number', $dprNumber)->exists()) {
            $nextSeq++;
            $dprNumber = sprintf('DPR-%s-%03d', $dateStr, $nextSeq);
        }

        $status = 'submitted';
        $approvedBy = null;
        $approvedAt = null;

        if ($request->input('action') === 'approve' && auth()->user()->can('dpr.approve')) {
            $status = 'approved';
            $approvedBy = auth()->id();
            $approvedAt = now();
        }

        $report = DailyProgressReport::create([
            'company_id' => $companyId,
            'project_id' => $validated['project_id'],
            'dpr_number' => $dprNumber,
            'report_date' => $validated['report_date'],
            'weather' => $validated['weather'],
            'site_engineer_id' => auth()->id(),
            'skilled_workers' => $validated['skilled_workers'],
            'unskilled_workers' => $validated['unskilled_workers'],
            'total_workers' => $validated['skilled_workers'] + $validated['unskilled_workers'],
            'work_performed' => $validated['work_performed'],
            'equipment_deployed' => $validated['equipment_deployed'] ?? null,
            'material_received_used' => $validated['material_received_used'] ?? null,
            'issues_safety_notes' => $validated['issues_safety_notes'] ?? null,
            'status' => $status,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
        ]);

        if (!empty($validated['activities'])) {
            foreach ($validated['activities'] as $act) {
                if (!empty($act['activity_name'])) {
                    $report->activities()->create([
                        'activity_name' => $act['activity_name'],
                        'boq_item_id' => !empty($act['boq_item_id']) ? $act['boq_item_id'] : null,
                        'location' => $act['location'] ?? null,
                        'quantity_completed' => $act['quantity_completed'],
                        'unit' => $act['unit'],
                        'remarks' => $act['remarks'] ?? null,
                    ]);
                }
            }
        }

        $successMsg = $status === 'approved'
            ? "Daily Progress Report {$dprNumber} submitted and approved successfully."
            : "Daily Progress Report {$dprNumber} submitted successfully.";

        return redirect()->route('dpr.show', $report)->with('success', $successMsg);
    }

    public function show(DailyProgressReport $dpr): View
    {
        $this->authorize('dpr.view');

        $dpr->load(['project', 'siteEngineer', 'approver', 'activities.boqItem']);

        return view('dpr.show', compact('dpr'));
    }

    public function approve(DailyProgressReport $dpr): RedirectResponse
    {
        $this->authorize('dpr.approve');

        if ($dpr->status === 'approved') {
            return back()->with('info', 'This Daily Progress Report is already approved.');
        }

        $dpr->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', "Daily Progress Report {$dpr->dpr_number} has been approved.");
    }
}
