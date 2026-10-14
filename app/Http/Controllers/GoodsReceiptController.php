<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GoodsReceiptController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('grn.view');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();
        $status = $request->string('status')->trim();

        $goodsReceipts = GoodsReceipt::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($status->isNotEmpty(), fn ($q) => $q->where('status', $status->value()))
            ->with(['project', 'purchaseOrder.supplier', 'warehouse', 'receiver', 'items.item'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('goods-receipts.index', compact('goodsReceipts', 'status'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('grn.create');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();

        $poId = $request->integer('po_id');
        $selectedPo = null;
        if ($poId) {
            $selectedPo = PurchaseOrder::with(['items.item.unit', 'supplier'])->find($poId);
        }

        $purchaseOrders = PurchaseOrder::where('company_id', $companyId)
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->whereIn('status', ['approved', 'partially_received'])
            ->with(['supplier', 'items.item'])
            ->get();

        $warehouses = Warehouse::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        return view('goods-receipts.create', compact('purchaseOrders', 'selectedPo', 'warehouses'));
    }

    public function store(StoreGoodsReceiptRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $companyId = $tenantManager->currentCompanyId();

        $po = PurchaseOrder::findOrFail($validated['purchase_order_id']);

        $grn = DB::transaction(function () use ($validated, $companyId, $po) {
            $grn = GoodsReceipt::create([
                'company_id' => $companyId,
                'project_id' => $po->project_id,
                'purchase_order_id' => $po->id,
                'warehouse_id' => $validated['warehouse_id'],
                'grn_number' => $validated['grn_number'],
                'received_date' => $validated['received_date'],
                'delivery_challan_no' => $validated['delivery_challan_no'],
                'supplier_invoice_no' => $validated['supplier_invoice_no'] ?? null,
                'status' => 'draft',
                'received_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $grn->items()->create([
                    'po_item_id' => $itemData['po_item_id'],
                    'item_id' => $itemData['item_id'],
                    'quantity_challan' => $itemData['quantity_challan'],
                    'quantity_received' => $itemData['quantity_received'],
                    'quantity_accepted' => $itemData['quantity_accepted'],
                    'quantity_rejected' => $itemData['quantity_rejected'] ?? 0.0000,
                    'unit_price' => $itemData['unit_price'],
                    'remarks' => $itemData['remarks'] ?? null,
                ]);
            }

            return $grn;
        });

        return redirect()->route('goods-receipts.show', $grn)
            ->with('success', 'পণ্য প্রাপ্তি নোট (GRN) সফলভাবে সংরক্ষিত হয়েছে।');
    }

    public function show(GoodsReceipt $goodsReceipt): View
    {
        $this->authorize('grn.view');

        $goodsReceipt->load([
            'project',
            'purchaseOrder.supplier',
            'warehouse',
            'receiver',
            'verifier',
            'items.item.unit',
        ]);

        return view('goods-receipts.show', compact('goodsReceipt'));
    }

    public function approve(GoodsReceipt $goodsReceipt): RedirectResponse
    {
        $this->authorize('grn.approve');

        if ($goodsReceipt->status === 'approved') {
            return back()->with('error', 'এই GRN ইতোমধ্যেই অনুমোদিত হয়েছে।');
        }

        $goodsReceipt->approveAndPostStock(auth()->id());

        return back()->with('success', 'GRN অনুমোদিত হয়েছে এবং গোডাউন স্টকে পণ্য যোগ হয়েছে।');
    }
}
