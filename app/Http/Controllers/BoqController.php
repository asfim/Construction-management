<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoqItemRequest;
use App\Models\BoqItem;
use App\Models\BoqVersion;
use App\Models\CostCode;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoqController extends Controller
{
    public function index(Project $project): View
    {
        $this->authorize('boq.view');

        $boqVersions = BoqVersion::where('project_id', $project->id)
            ->with(['creator', 'approver'])
            ->withCount('items')
            ->orderByDesc('version_number')
            ->get();

        return view('boqs.index', compact('project', 'boqVersions'));
    }

    public function store(Request $request, Project $project, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('boq.manage');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'remarks' => 'nullable|string|max:500',
        ]);

        $latestVersionNumber = BoqVersion::where('project_id', $project->id)->max('version_number') ?? 0;
        $newVersionNumber = $latestVersionNumber + 1;

        $boq = BoqVersion::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'version_number' => $newVersionNumber,
            'title' => $validated['title'],
            'remarks' => $validated['remarks'] ?? ($newVersionNumber === 1 ? 'Initial project estimation baseline' : null),
            'total_amount' => 0,
            'status' => 'draft',
            'is_locked' => false,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projects.boqs.show', [$project, $boq])
            ->with('success', "BOQ Version {$newVersionNumber} created successfully. You can now add work items.");
    }

    public function show(Project $project, BoqVersion $boq): View
    {
        $this->authorize('boq.view');

        abort_if($boq->project_id !== $project->id, 404);

        $boq->load(['items.costCode', 'items.rateAnalysis', 'creator', 'approver']);
        $costCodes = CostCode::where('is_active', true)->get();

        return view('boqs.show', compact('project', 'boq', 'costCodes'));
    }

    public function storeItem(StoreBoqItemRequest $request, Project $project, BoqVersion $boq): RedirectResponse
    {
        abort_if($boq->project_id !== $project->id, 404);

        if ($boq->is_locked) {
            return back()->with('error', 'This BOQ version is approved and locked. To add items, create a new revision.');
        }

        $validated = $request->validated();
        $validated['boq_version_id'] = $boq->id;
        $validated['project_id'] = $project->id;

        BoqItem::create($validated);

        return back()->with('success', __('erp.messages.created_successfully'));
    }

    public function createRevision(Project $project, BoqVersion $boq, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('boq.manage');

        abort_if($boq->project_id !== $project->id, 404);

        $latestVersionNumber = BoqVersion::where('project_id', $project->id)->max('version_number') ?? 1;
        $newVersionNumber = $latestVersionNumber + 1;

        $newBoq = BoqVersion::create([
            'company_id' => $tenantManager->currentCompanyId(),
            'project_id' => $project->id,
            'version_number' => $newVersionNumber,
            'title' => "Variation Revision {$newVersionNumber}",
            'total_amount' => $boq->total_amount,
            'status' => 'draft',
            'is_locked' => false,
            'created_by' => auth()->id(),
            'remarks' => "Cloned from approved Version {$boq->version_number} for contract variations.",
        ]);

        // Duplicate items
        foreach ($boq->items as $item) {
            $newItem = $item->replicate();
            $newItem->boq_version_id = $newBoq->id;
            $newItem->save();
        }

        return redirect()->route('projects.boqs.show', [$project, $newBoq])
            ->with('success', "New BOQ Revision {$newVersionNumber} created successfully. Baseline preserved.");
    }

    public function approve(Project $project, BoqVersion $boq): RedirectResponse
    {
        $this->authorize('boq.approve');

        abort_if($boq->project_id !== $project->id, 404);

        if ($boq->is_locked) {
            return back()->with('error', 'This BOQ version is already approved.');
        }

        $boq->update([
            'status' => 'approved',
            'is_locked' => true,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'BOQ version approved and baseline locked successfully.');
    }
}
