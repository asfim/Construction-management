<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Item;
use App\Models\MaterialRequisition;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('po.view');

        $companyId = $tenantManager->currentCompanyId();
        
        if ($request->has('project_id')) {
            $selectedProj = $request->get('project_id');
            $projectId = ($selectedProj === 'all' || empty($selectedProj)) ? null : (int) $selectedProj;
        } else {
            $projectId = $tenantManager->currentProjectId();
        }

        $status = $request->string('status')->trim()->value();

        // Default to unapproved (draft) if no status was specified
        if (! $request->has('status') || $status === '') {
            $status = 'unapproved';
        }

        $baseQuery = PurchaseOrder::withoutGlobalScope(\App\Models\Scopes\ProjectScope::class)
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId));

        $counts = [
            'unapproved' => (clone $baseQuery)->where('status', 'draft')->count(),
            'approved' => (clone $baseQuery)->whereIn('status', ['approved', 'partially_received', 'received'])->count(),
            'all' => (clone $baseQuery)->count(),
        ];

        $purchaseOrders = (clone $baseQuery)
            ->when($status === 'unapproved' || $status === 'draft', fn ($q) => $q->where('status', 'draft'))
            ->when($status === 'approved', fn ($q) => $q->whereIn('status', ['approved', 'partially_received', 'received']))
            ->when(! in_array($status, ['unapproved', 'draft', 'approved', 'all']), fn ($q) => $q->where('status', $status))
            ->with(['project', 'supplier', 'creator', 'approver', 'items.item'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('purchase-orders.index', compact('purchaseOrders', 'status', 'counts'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('po.create');

        $companyId = $tenantManager->currentCompanyId();
        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $suppliers = Supplier::where('company_id', $companyId)->where('is_active', true)->get();
        $items = Item::where('company_id', $companyId)->where('is_active', true)->with('unit')->get();

        $mr = null;
        if ($request->has('mr_id')) {
            $mr = MaterialRequisition::with('items.item')->find($request->get('mr_id'));
        }

        return view('purchase-orders.create', compact('projects', 'suppliers', 'items', 'mr'));
    }

    public function store(StorePurchaseOrderRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $companyId = $tenantManager->currentCompanyId();

        if (! empty($validated['project_id'])) {
            $tenantManager->setProject($validated['project_id']);
        }

        $po = DB::transaction(function () use ($validated, $companyId) {
            $subtotal = 0.00;
            foreach ($validated['items'] as $itemData) {
                $subtotal += round($itemData['quantity_ordered'] * $itemData['unit_price'], 2);
            }

            $vatPercentage = $validated['vat_percentage'] ?? 7.50;
            $vatAmount = round($subtotal * ($vatPercentage / 100), 2);
            $totalAmount = round($subtotal + $vatAmount, 2);

            $po = PurchaseOrder::create([
                'company_id' => $companyId,
                'project_id' => $validated['project_id'],
                'supplier_id' => $validated['supplier_id'],
                'po_number' => $validated['po_number'],
                'po_date' => $validated['po_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'delivery_address' => $validated['delivery_address'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'subtotal' => $subtotal,
                'vat_percentage' => $vatPercentage,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $itemTotal = round($itemData['quantity_ordered'] * $itemData['unit_price'], 2);
                $po->items()->create([
                    'item_id' => $itemData['item_id'],
                    'mr_item_id' => $itemData['mr_item_id'] ?? null,
                    'quantity_ordered' => $itemData['quantity_ordered'],
                    'quantity_received' => 0.0000,
                    'unit_price' => $itemData['unit_price'],
                    'total_amount' => $itemTotal,
                ]);
            }

            return $po;
        });

        return redirect()->route('purchase-orders.show', $po)
            ->with('success', 'Purchase Order (PO) created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->authorize('po.view');

        $purchaseOrder->load([
            'project',
            'supplier',
            'items.item.unit',
            'goodsReceipts.items',
            'creator',
            'approver',
        ]);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function approve(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->authorize('po.approve');

        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Only draft purchase orders can be approved.');
        }

        $purchaseOrder->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase Order (PO) approved successfully.');
    }
}
