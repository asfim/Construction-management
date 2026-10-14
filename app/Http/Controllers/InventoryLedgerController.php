<?php

namespace App\Http\Controllers;

use App\Models\CostCode;
use App\Models\InventoryTransaction;
use App\Models\Item;
use App\Models\Project;
use App\Models\StockIssue;
use App\Models\Warehouse;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryLedgerController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('stock.view');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();

        $selectedWarehouseId = $request->integer('warehouse_id');
        $selectedItemId = $request->integer('item_id');

        $warehouses = Warehouse::where('company_id', $companyId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId)->orWhere('is_central', true))
            ->where('is_active', true)
            ->get();

        // Calculate Stock Balances Per Item
        $stockSummaryQuery = InventoryTransaction::query()
            ->select(
                'item_id',
                'warehouse_id',
                DB::raw('SUM(quantity) as current_stock'),
                DB::raw('AVG(unit_cost) as avg_cost'),
                DB::raw('SUM(quantity * unit_cost) as total_valuation')
            )
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($selectedWarehouseId, fn ($q) => $q->where('warehouse_id', $selectedWarehouseId))
            ->when($selectedItemId, fn ($q) => $q->where('item_id', $selectedItemId))
            ->groupBy('item_id', 'warehouse_id')
            ->having('current_stock', '>', 0)
            ->with(['item.unit', 'warehouse']);

        $stockBalances = $stockSummaryQuery->get();

        // Recent Stock Movement Transactions
        $transactions = InventoryTransaction::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($selectedWarehouseId, fn ($q) => $q->where('warehouse_id', $selectedWarehouseId))
            ->when($selectedItemId, fn ($q) => $q->where('item_id', $selectedItemId))
            ->with(['item.unit', 'warehouse', 'project', 'creator'])
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $items = Item::where('company_id', $companyId)->where('is_active', true)->get();

        return view('inventory.ledger', compact(
            'warehouses',
            'items',
            'stockBalances',
            'transactions',
            'selectedWarehouseId',
            'selectedItemId'
        ));
    }

    public function createIssue(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('stock.issue');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();

        $warehouses = Warehouse::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $projects = Project::where('company_id', $companyId)
            ->where('status', 'active')
            ->get();

        $costCodes = CostCode::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $items = Item::where('company_id', $companyId)->where('is_active', true)->with('unit')->get();

        return view('inventory.create-issue', compact('warehouses', 'projects', 'costCodes', 'items'));
    }

    public function storeIssue(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('stock.issue');

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'issue_no' => ['required', 'string', 'max:50'],
            'issue_date' => ['required', 'date'],
            'issued_to_contractor_or_labor' => ['required', 'string', 'max:255'],
            'cost_code_id' => ['nullable', 'exists:cost_codes,id'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity_issued' => ['required', 'numeric', 'min:0.0001'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        $companyId = $tenantManager->currentCompanyId();

        $warehouse = Warehouse::findOrFail($validated['warehouse_id']);

        // Check if sufficient stock exists for each item
        foreach ($validated['items'] as $itemData) {
            $currentStock = $warehouse->stockOfItem($itemData['item_id']);
            if ($currentStock < $itemData['quantity_issued']) {
                $item = Item::find($itemData['item_id']);
                return back()->withInput()->with('error', "গোডাউনে '{$item?->name}' এর পর্যাপ্ত স্টক নেই। বর্তমান মজুদ: {$currentStock}, রিকোয়েস্ট: {$itemData['quantity_issued']}");
            }
        }

        $stockIssue = DB::transaction(function () use ($validated, $companyId) {
            $issue = StockIssue::create([
                'company_id' => $companyId,
                'project_id' => $validated['project_id'],
                'warehouse_id' => $validated['warehouse_id'],
                'issue_no' => $validated['issue_no'],
                'issue_date' => $validated['issue_date'],
                'issued_to_contractor_or_labor' => $validated['issued_to_contractor_or_labor'],
                'cost_code_id' => $validated['cost_code_id'] ?? null,
                'status' => 'approved',
                'issued_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $unitCost = $itemData['unit_cost'];
                $totalCost = round($itemData['quantity_issued'] * $unitCost, 2);

                $issue->items()->create([
                    'item_id' => $itemData['item_id'],
                    'quantity_issued' => $itemData['quantity_issued'],
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                ]);
            }

            // Record transaction and deduct stock
            $issue->approveAndDeductStock(auth()->id());

            return $issue;
        });

        return redirect()->route('inventory.ledger')
            ->with('success', "স্টক ইস্যু #{$stockIssue->issue_no} সফলভাবে সম্পন্ন হয়েছে এবং গোডাউন স্টক হালনাগাদ করা হয়েছে।");
    }
}
