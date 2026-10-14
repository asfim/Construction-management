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
        $projectId = $tenantManager->currentProjectId();
        $status = $request->string('status')->trim();

        $requisitions = MaterialRequisition::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($status->isNotEmpty(), fn ($q) => $q->where('status', $status->value()))
            ->with(['project', 'submittedBy', 'items.item'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('material-requisitions.index', compact('requisitions', 'status'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('mr.create');

        $companyId = $tenantManager->currentCompanyId();
        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $items = Item::where('company_id', $companyId)->where('is_active', true)->with('unit')->get();
        $costCodes = CostCode::where('company_id', $companyId)->where('is_active', true)->get();

        return view('material-requisitions.create', compact('projects', 'items', 'costCodes'));
    }

    public function store(StoreMaterialRequisitionRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $validated['company_id'] = $tenantManager->currentCompanyId();
        $validated['submitted_by'] = auth()->id();
        $validated['status'] = 'submitted';

        $mr = MaterialRequisition::create($validated);

        foreach ($validated['items'] as $itemData) {
            $itemData['quantity_approved'] = $itemData['quantity_requested'];
            $mr->items()->create($itemData);
        }

        return redirect()->route('material-requisitions.show', $mr)
            ->with('success', 'উপকরণ চাহিদা (MR) সফলভাবে দাখিল করা হয়েছে।');
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
            $msg = 'প্রকল্প ব্যবস্থাপক কর্তৃক উপকরণ চাহিদা অনুমোদিত হয়েছে (PM Approved)।';
        } else if ($materialRequisition->status === 'pm_approved') {
            $materialRequisition->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
            $msg = 'উপকরণ চাহিদা চূড়ান্তভাবে অনুমোদিত হয়েছে (Final Approved)।';
        } else {
            return back()->with('error', 'এই রিকুইজিশনটি ইতোমধ্যেই অনুমোদিত অথবা বাতিল করা হয়েছে।');
        }

        return back()->with('success', $msg);
    }
}
