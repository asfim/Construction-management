<?php

use App\Models\Company;
use App\Models\CostCode;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\MaterialRequisition;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;

test('site engineer can submit material requisition and pm can approve it', function () {
    $siteEngineer = User::where('email', 'site.engineer@htech.com.bd')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();
    $item = Item::first();

    // 1. Submit MR
    $response = $this->actingAs($siteEngineer)->post(route('material-requisitions.store'), [
        'project_id' => $project->id,
        'requisition_no' => 'MR-TEST-001',
        'requisition_date' => now()->toDateString(),
        'items' => [
            [
                'item_id' => $item->id,
                'quantity_requested' => 10.0000,
                'remarks' => 'Test site requisition',
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('material_requisitions', [
        'requisition_no' => 'MR-TEST-001',
        'status' => 'submitted',
    ]);

    $mr = MaterialRequisition::where('requisition_no', 'MR-TEST-001')->first();

    // 2. PM approves MR
    $approveResponse = $this->actingAs($pm)->post(route('material-requisitions.approve', $mr));
    $approveResponse->assertRedirect();

    $mr->refresh();
    expect($mr->status)->toBe('pm_approved');
});

test('procurement manager can create and approve purchase order with 7.5 percent VAT', function () {
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();
    $project = Project::first();
    $supplier = Supplier::first();
    $item = Item::first();

    $response = $this->actingAs($procurement)->post(route('purchase-orders.store'), [
        'project_id' => $project->id,
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-TEST-VAT-01',
        'po_date' => now()->toDateString(),
        'vat_percentage' => 7.50,
        'items' => [
            [
                'item_id' => $item->id,
                'quantity_ordered' => 100.0000,
                'unit_price' => 500.00, // 100 * 500 = 50,000 subtotal. VAT 7.5% = 3,750. Total = 53,750
            ],
        ],
    ]);

    $response->assertRedirect();

    $po = PurchaseOrder::where('po_number', 'PO-TEST-VAT-01')->first();
    expect($po)->not->toBeNull();
    expect((float) $po->subtotal)->toBe(50000.00);
    expect((float) $po->vat_amount)->toBe(3750.00);
    expect((float) $po->total_amount)->toBe(53750.00);
    expect($po->status)->toBe('draft');

    // Approve PO
    $this->actingAs($procurement)->post(route('purchase-orders.approve', $po));
    $po->refresh();
    expect($po->status)->toBe('approved');
});

test('goods receipt note updates po received quantity and posts inventory stock transaction', function () {
    $storekeeper = User::where('email', 'storekeeper@htech.com.bd')->first();
    $project = Project::first();
    $po = PurchaseOrder::where('status', 'partially_received')->first();

    if (!$po) {
        $po = PurchaseOrder::first();
    }

    $warehouse = Warehouse::first();
    $poItem = $po->items()->first();

    $grnResponse = $this->actingAs($storekeeper)->post(route('goods-receipts.store'), [
        'purchase_order_id' => $po->id,
        'warehouse_id' => $warehouse->id,
        'grn_number' => 'GRN-TEST-002',
        'received_date' => now()->toDateString(),
        'delivery_challan_no' => 'CH-TEST-7788',
        'items' => [
            [
                'po_item_id' => $poItem->id,
                'item_id' => $poItem->item_id,
                'quantity_challan' => 5.0000,
                'quantity_received' => 5.0000,
                'quantity_accepted' => 5.0000,
                'quantity_rejected' => 0.0000,
                'unit_price' => (float) $poItem->unit_price,
            ],
        ],
    ]);

    $grnResponse->assertRedirect();
    $grn = GoodsReceipt::where('grn_number', 'GRN-TEST-002')->first();
    expect($grn)->not->toBeNull();

    // Approve GRN
    $initialStock = $warehouse->stockOfItem($poItem->item_id);
    $initialReceived = (float) $poItem->fresh()->quantity_received;

    $this->actingAs($storekeeper)->post(route('goods-receipts.approve', $grn));

    $grn->refresh();
    expect($grn->status)->toBe('approved');

    // Verify PO Item Received Quantity Incremented
    expect((float) $poItem->fresh()->quantity_received)->toBe($initialReceived + 5.0000);

    // Verify Warehouse stock incremented
    expect($warehouse->stockOfItem($poItem->item_id))->toBe($initialStock + 5.0000);
});

test('store issue reduces inventory and rejects issue if stock is insufficient', function () {
    $storekeeper = User::where('email', 'storekeeper@htech.com.bd')->first();
    $warehouse = Warehouse::first();
    $item = Item::first();
    $project = Project::first();

    $currentStock = $warehouse->stockOfItem($item->id);

    // Attempt to issue more than available stock
    $excessiveQty = $currentStock + 99999;

    $failResponse = $this->actingAs($storekeeper)->post(route('inventory.issues.store'), [
        'project_id' => $project->id,
        'warehouse_id' => $warehouse->id,
        'issue_no' => 'SIN-FAIL-01',
        'issue_date' => now()->toDateString(),
        'issued_to_contractor_or_labor' => 'Excessive Labor Gang',
        'items' => [
            [
                'item_id' => $item->id,
                'quantity_issued' => $excessiveQty,
                'unit_cost' => 100.00,
            ],
        ],
    ]);

    $failResponse->assertSessionHas('error');
    $this->assertDatabaseMissing('stock_issues', ['issue_no' => 'SIN-FAIL-01']);
});
