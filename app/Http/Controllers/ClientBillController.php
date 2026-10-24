<?php

namespace App\Http\Controllers;

use App\Models\BoqItem;
use App\Models\ClientBill;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientBillController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('bill.view');

        $companyId = $tenantManager->currentCompanyId();
        
        if ($request->has('project_id')) {
            $selectedProj = $request->get('project_id');
            $projectId = ($selectedProj === 'all' || empty($selectedProj)) ? null : (int) $selectedProj;
        } else {
            $projectId = $tenantManager->currentProjectId();
        }

        $status = $request->string('status')->trim()->value();

        // Default to uncertified (submitted) if no status was specified
        if (! $request->has('status') || $status === '') {
            $status = 'unapproved';
        }

        $baseQuery = ClientBill::withoutGlobalScope(\App\Models\Scopes\ProjectScope::class)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

        $counts = [
            'unapproved' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $baseQuery)->whereIn('status', ['certified', 'paid'])->count(),
            'all' => (clone $baseQuery)->count(),
        ];

        $bills = (clone $baseQuery)
            ->when($status === 'unapproved' || $status === 'submitted', fn ($q) => $q->where('status', 'submitted'))
            ->when($status === 'approved', fn ($q) => $q->whereIn('status', ['certified', 'paid']))
            ->when($status === 'certified', fn ($q) => $q->where('status', 'certified'))
            ->when($status === 'paid', fn ($q) => $q->where('status', 'paid'))
            ->when(! in_array($status, ['unapproved', 'submitted', 'approved', 'certified', 'paid', 'all']), fn ($q) => $q->where('status', $status))
            ->with(['project', 'creator', 'certifier', 'items'])
            ->latest('bill_date')
            ->paginate(15)
            ->withQueryString();

        return view('client-bills.index', compact('bills', 'status', 'counts'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('bill.create');

        $companyId = $tenantManager->currentCompanyId();
        $activeProjectId = $tenantManager->currentProjectId();

        $projects = Project::where('company_id', $companyId)
            ->whereIn('status', ['active', 'in_progress', 'planning'])
            ->get();

        $selectedProjectId = $request->integer('project_id') ?: $activeProjectId ?: $projects->first()?->id;
        $project = $projects->firstWhere('id', $selectedProjectId) ?? Project::find($selectedProjectId);

        // Fetch latest active/approved BOQ Version
        $boqVersion = null;
        $boqItems = collect();
        if ($project) {
            $boqVersion = \App\Models\BoqVersion::where('project_id', $project->id)
                ->where('is_locked', true)
                ->orderByDesc('version_number')
                ->first()
                ?? \App\Models\BoqVersion::where('project_id', $project->id)
                    ->orderByDesc('version_number')
                    ->first();

            if ($boqVersion) {
                $boqItems = $boqVersion->items()->with('costCode')->get();
            }
        }

        // Calculate previously billed quantities for each BOQ item
        $previousQuantities = [];
        if ($project) {
            foreach ($boqItems as $item) {
                $prev = \App\Models\ClientBillItem::whereHas('clientBill', function ($q) use ($project) {
                    $q->where('project_id', $project->id)
                      ->whereIn('status', ['certified', 'paid']);
                })->where('boq_item_id', $item->id)->sum('current_quantity');

                $previousQuantities[$item->id] = (float) $prev;
            }
        }

        // Compute next available bill number for this project (e.g. RA-01, RA-02)
        $nextNum = 1;
        if ($project) {
            $existingBillCount = ClientBill::where('company_id', $companyId)->where('project_id', $project->id)->count();
            $nextNum = $existingBillCount + 1;
            while (ClientBill::where('company_id', $companyId)->where('project_id', $project->id)->where('bill_number', 'RA-' . sprintf('%02d', $nextNum))->exists()) {
                $nextNum++;
            }
        }
        $nextBillNumber = 'RA-' . sprintf('%02d', $nextNum);

        return view('client-bills.create', compact('projects', 'project', 'selectedProjectId', 'boqVersion', 'boqItems', 'previousQuantities', 'nextBillNumber'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('bill.create');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $request->input('project_id');

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'bill_number' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('client_bills', 'bill_number')
                    ->where('company_id', $companyId)
                    ->where('project_id', $projectId),
            ],
            'bill_date' => 'required|date',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'advance_recovery_percentage' => 'required|numeric|min:0|max:100',
            'retention_percentage' => 'required|numeric|min:0|max:100',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.boq_item_id' => 'required|exists:boq_items,id',
            'items.*.unit_rate' => 'required|numeric|min:0',
            'items.*.previous_quantity' => 'required|numeric|min:0',
            'items.*.current_quantity' => 'required|numeric|min:0',
            'items.*.remarks' => 'nullable|string|max:255',
        ]);

        $grossAmount = 0;
        foreach ($validated['items'] as $itemData) {
            $currentAmount = (float) $itemData['current_quantity'] * (float) $itemData['unit_rate'];
            $grossAmount += $currentAmount;
        }

        if ($grossAmount <= 0) {
            return back()->withInput()->withErrors([
                'items' => 'Please enter a current measured quantity greater than 0 for at least one BOQ item.',
            ]);
        }

        $advanceRecoveryAmount = round($grossAmount * ($validated['advance_recovery_percentage'] / 100), 2);
        $retentionAmount = round($grossAmount * ($validated['retention_percentage'] / 100), 2);
        $vatAmount = round($grossAmount * ($validated['vat_percentage'] / 100), 2);
        $netPayable = round($grossAmount - $advanceRecoveryAmount - $retentionAmount + $vatAmount, 2);

        if (! empty($validated['project_id'])) {
            $tenantManager->setProject($validated['project_id']);
        }

        $bill = ClientBill::create([
            'company_id' => $companyId,
            'project_id' => $validated['project_id'],
            'bill_number' => $validated['bill_number'],
            'bill_date' => $validated['bill_date'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'gross_amount' => $grossAmount,
            'advance_recovery_percentage' => $validated['advance_recovery_percentage'],
            'advance_recovery_amount' => $advanceRecoveryAmount,
            'retention_percentage' => $validated['retention_percentage'],
            'retention_amount' => $retentionAmount,
            'vat_percentage' => $validated['vat_percentage'],
            'vat_amount' => $vatAmount,
            'net_payable' => $netPayable,
            'status' => 'submitted',
            'created_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($validated['items'] as $itemData) {
            $boqItem = BoqItem::find($itemData['boq_item_id']);
            $currentAmount = round((float) $itemData['current_quantity'] * (float) $itemData['unit_rate'], 2);
            $totalQuantity = (float) $itemData['previous_quantity'] + (float) $itemData['current_quantity'];

            $bill->items()->create([
                'boq_item_id' => $itemData['boq_item_id'],
                'description' => $boqItem?->description ?? 'Line Item',
                'unit' => $boqItem?->unit ?? 'unit',
                'unit_rate' => $itemData['unit_rate'],
                'previous_quantity' => $itemData['previous_quantity'],
                'current_quantity' => $itemData['current_quantity'],
                'total_quantity' => $totalQuantity,
                'current_amount' => $currentAmount,
                'remarks' => $itemData['remarks'] ?? null,
            ]);
        }

        return redirect()->route('client-bills.show', $bill)
            ->with('success', "RA Bill {$bill->bill_number} created successfully.");
    }

    public function show(ClientBill $clientBill): View
    {
        $this->authorize('bill.view');

        $clientBill->load(['project', 'creator', 'certifier', 'items.boqItem']);

        return view('client-bills.show', compact('clientBill'));
    }

    public function certify(ClientBill $clientBill): RedirectResponse
    {
        $this->authorize('bill.certify');

        if ($clientBill->status === 'certified') {
            return back()->with('info', 'This bill is already certified.');
        }

        $clientBill->update([
            'status' => 'certified',
            'certified_by' => auth()->id(),
            'certified_at' => now(),
        ]);

        return back()->with('success', "RA Bill {$clientBill->bill_number} has been certified.");
    }

    public function markPaid(ClientBill $clientBill): RedirectResponse
    {
        $this->authorize('bill.certify');

        if ($clientBill->status === 'paid') {
            return back()->with('info', 'This bill is already marked as paid.');
        }

        $clientBill->update([
            'status' => 'paid',
        ]);

        return back()->with('success', "RA Bill {$clientBill->bill_number} marked as paid.");
    }
}
