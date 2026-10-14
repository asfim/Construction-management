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
        $status = $request->string('status')->trim();

        $reports = DailyProgressReport::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($status->isNotEmpty(), fn ($q) => $q->where('status', $status->value()))
            ->with(['project', 'siteEngineer', 'approver', 'activities'])
            ->latest('report_date')
            ->paginate(15)
            ->withQueryString();

        return view('dpr.index', compact('reports', 'status'));
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
            'activities.*.activity_name' => 'required_with:activities|string|max:255',
            'activities.*.location' => 'nullable|string|max:255',
            'activities.*.quantity_completed' => 'required_with:activities|numeric|min:0',
            'activities.*.unit' => 'required_with:activities|string|max:20',
            'activities.*.boq_item_id' => 'nullable|exists:boq_items,id',
            'activities.*.remarks' => 'nullable|string|max:255',
        ]);

        $companyId = $tenantManager->currentCompanyId();
        $dateStr = str_replace('-', '', $validated['report_date']);
        $count = DailyProgressReport::where('company_id', $companyId)->where('dpr_number', 'LIKE', "DPR-{$dateStr}-%")->count() + 1;
        $dprNumber = sprintf('DPR-%s-%03d', $dateStr, $count);
        while (DailyProgressReport::where('company_id', $companyId)->where('dpr_number', $dprNumber)->exists()) {
            $count++;
            $dprNumber = sprintf('DPR-%s-%03d', $dateStr, $count);
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
            'status' => 'submitted',
        ]);

        if (!empty($validated['activities'])) {
            foreach ($validated['activities'] as $act) {
                if (!empty($act['activity_name'])) {
                    $report->activities()->create($act);
                }
            }
        }

        return redirect()->route('dpr.show', $report)
            ->with('success', "Daily Progress Report {$dprNumber} submitted successfully.");
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
