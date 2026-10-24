<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequisitionRequest;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\MaterialRequisition;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialRequisitionController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('mr.view');

        $companyId = $tenantManager->currentCompanyId();
        
        if ($request->has('project_id')) {
            $selectedProj = $request->get('project_id');
            $projectId = ($selectedProj === 'all' || empty($selectedProj)) ? null : (int) $selectedProj;
        } else {
            $projectId = $tenantManager->currentProjectId();
        }

        $status = $request->string('status')->trim()->value();

        // Default to unapproved if no status filter was specified
        if (! $request->has('status') || $status === '') {
            $status = 'unapproved';
        }

        $baseQuery = MaterialRequisition::withoutGlobalScope(\App\Models\Scopes\ProjectScope::class)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

        $counts = [
            'unapproved' => (clone $baseQuery)->where('status', '!=', 'approved')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'all' => (clone $baseQuery)->count(),
        ];

        $requisitions = (clone $baseQuery)
            ->when($status === 'unapproved', fn ($q) => $q->where('status', '!=', 'approved'))
            ->when($status === 'approved', fn ($q) => $q->where('status', 'approved'))
            ->when(! in_array($status, ['unapproved', 'approved', 'all']), fn ($q) => $q->where('status', $status))
            ->with(['project', 'submittedBy', 'items.item'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $currentProject = $projectId ? Project::find($projectId) : null;

        return view('material-requisitions.index', compact('requisitions', 'status', 'counts', 'projects', 'projectId', 'currentProject'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('mr.create');

        $companyId = $tenantManager->currentCompanyId();
        $currentProjectId = $tenantManager->currentProjectId();
        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $items = Item::where('company_id', $companyId)->where('is_active', true)->with('unit')->get();
        $costCodes = CostCode::where('company_id', $companyId)->where('is_active', true)->get();

        return view('material-requisitions.create', compact('projects', 'items', 'costCodes', 'currentProjectId'));
    }

    public function store(StoreMaterialRequisitionRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $validated['company_id'] = $tenantManager->currentCompanyId();
        $validated['submitted_by'] = auth()->id();
        $validated['status'] = 'submitted';

        if (! empty($validated['project_id'])) {
            $tenantManager->setProject($validated['project_id']);
        }

        $mr = MaterialRequisition::create($validated);

        foreach ($validated['items'] as $itemData) {
            $itemData['quantity_approved'] = $itemData['quantity_requested'];
            $mr->items()->create($itemData);
        }

        return redirect()->route('material-requisitions.show', $mr)
            ->with('success', 'Material Requisition (MR) submitted successfully.');
    }

    public function show(MaterialRequisition $materialRequisition): View
    {
        $this->authorize('mr.view');

        $materialRequisition->load(['project', 'items.item.unit', 'items.costCode', 'submittedBy', 'pmApprovedBy', 'approvedBy']);

        return view('material-requisitions.show', compact('materialRequisition'));
    }

    public function approve(MaterialRequisition $materialRequisition): RedirectResponse
    {
        $this->authorize('mr.approve');

        $user = auth()->user();

        if ($materialRequisition->status === 'submitted') {
            $materialRequisition->update([
                'status' => 'pm_approved',
                'pm_approved_by' => $user->id,
                'pm_approved_at' => now(),
            ]);
            $msg = 'Material requisition approved by Project Manager (PM Approved).';
        } else if ($materialRequisition->status === 'pm_approved') {
            $materialRequisition->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            $msg = 'Material requisition has been final approved.';
        } else {
            return back()->with('error', 'This requisition is already approved or cancelled.');
        }

        return back()->with('success', $msg);
    }
}
