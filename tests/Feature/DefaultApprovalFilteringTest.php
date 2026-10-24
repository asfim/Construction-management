<?php

use App\Models\ClientBill;
use App\Models\Company;
use App\Models\DailyProgressReport;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\MaterialRequisition;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;

test('material requisitions index defaults to unapproved items and respects status filter', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();

    // Create an unapproved (submitted) MR
    MaterialRequisition::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'requisition_no' => 'MR-FILTER-PENDING-01',
        'requisition_date' => now()->toDateString(),
        'status' => 'submitted',
        'requested_by' => $pm->id,
    ]);

    // Create an approved MR
    MaterialRequisition::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'requisition_no' => 'MR-FILTER-APPROVED-01',
        'requisition_date' => now()->toDateString(),
        'status' => 'approved',
        'requested_by' => $pm->id,
        'approved_by' => $pm->id,
        'approved_at' => now(),
    ]);

    // 1. Default listing without status query param should show unapproved and hide approved
    $responseDefault = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('material-requisitions.index'));

    $responseDefault->assertOk();
    $responseDefault->assertSee('MR-FILTER-PENDING-01');
    $responseDefault->assertDontSee('MR-FILTER-APPROVED-01');

    // 2. Listing with ?status=approved should show approved and hide unapproved
    $responseApproved = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('material-requisitions.index', ['status' => 'approved']));

    $responseApproved->assertOk();
    $responseApproved->assertSee('MR-FILTER-APPROVED-01');
    $responseApproved->assertDontSee('MR-FILTER-PENDING-01');

    // 3. Listing with ?status=all should show both
    $responseAll = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('material-requisitions.index', ['status' => 'all']));

    $responseAll->assertOk();
    $responseAll->assertSee('MR-FILTER-PENDING-01');
    $responseAll->assertSee('MR-FILTER-APPROVED-01');
});

test('purchase orders index defaults to draft unapproved orders and respects status filter', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();
    $project = Project::first();
    $supplier = Supplier::first();

    // Draft unapproved PO
    PurchaseOrder::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-FILTER-DRAFT-01',
        'po_date' => now()->toDateString(),
        'subtotal' => 1000,
        'vat_amount' => 75,
        'grand_total' => 1075,
        'status' => 'draft',
        'created_by' => $procurement->id,
    ]);

    // Approved PO
    PurchaseOrder::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'supplier_id' => $supplier->id,
        'po_number' => 'PO-FILTER-APPROVED-01',
        'po_date' => now()->toDateString(),
        'subtotal' => 2000,
        'vat_amount' => 150,
        'grand_total' => 2150,
        'status' => 'approved',
        'created_by' => $procurement->id,
        'approved_by' => $procurement->id,
        'approved_at' => now(),
    ]);

    // 1. Default should show draft and hide approved
    $resDefault = $this->actingAs($procurement)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('purchase-orders.index'));

    $resDefault->assertOk();
    $resDefault->assertSee('PO-FILTER-DRAFT-01');
    $resDefault->assertDontSee('PO-FILTER-APPROVED-01');

    // 2. Status = approved should show approved and hide draft
    $resApproved = $this->actingAs($procurement)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('purchase-orders.index', ['status' => 'approved']));

    $resApproved->assertOk();
    $resApproved->assertSee('PO-FILTER-APPROVED-01');
    $resApproved->assertDontSee('PO-FILTER-DRAFT-01');

    // 3. Status = all should show both
    $resAll = $this->actingAs($procurement)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('purchase-orders.index', ['status' => 'all']));

    $resAll->assertOk();
    $resAll->assertSee('PO-FILTER-DRAFT-01');
    $resAll->assertSee('PO-FILTER-APPROVED-01');
});

test('goods receipts index defaults to draft unapproved notes and respects status filter', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $storekeeper = User::where('email', 'storekeeper@htech.com.bd')->first();
    $project = Project::first();
    $supplier = Supplier::first();
    $warehouse = Warehouse::first();
    $po = PurchaseOrder::first();

    GoodsReceipt::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'purchase_order_id' => $po->id,
        'warehouse_id' => $warehouse->id,
        'grn_number' => 'GRN-FILTER-DRAFT-01',
        'received_date' => now()->toDateString(),
        'delivery_challan_no' => 'CH-001',
        'status' => 'draft',
        'received_by' => $storekeeper->id,
    ]);

    GoodsReceipt::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'purchase_order_id' => $po->id,
        'warehouse_id' => $warehouse->id,
        'grn_number' => 'GRN-FILTER-APPROVED-01',
        'received_date' => now()->toDateString(),
        'delivery_challan_no' => 'CH-002',
        'status' => 'approved',
        'received_by' => $storekeeper->id,
        'verified_by' => $storekeeper->id,
        'verified_at' => now(),
    ]);

    // 1. Default should show draft and hide approved
    $resDefault = $this->actingAs($storekeeper)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('goods-receipts.index'));

    $resDefault->assertOk();
    $resDefault->assertSee('GRN-FILTER-DRAFT-01');
    $resDefault->assertDontSee('GRN-FILTER-APPROVED-01');

    // 2. Status = approved
    $resApproved = $this->actingAs($storekeeper)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('goods-receipts.index', ['status' => 'approved']));

    $resApproved->assertOk();
    $resApproved->assertSee('GRN-FILTER-APPROVED-01');
    $resApproved->assertDontSee('GRN-FILTER-DRAFT-01');
});

test('daily progress reports index defaults to unapproved reports and respects status filter', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();

    DailyProgressReport::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'site_engineer_id' => $pm->id,
        'dpr_number' => 'DPR-FILTER-SUBMITTED-01',
        'report_date' => now()->toDateString(),
        'weather' => 'Sunny',
        'skilled_workers' => 5,
        'unskilled_workers' => 10,
        'total_workers' => 15,
        'work_performed' => 'Substructure work',
        'status' => 'submitted',
    ]);

    DailyProgressReport::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'site_engineer_id' => $pm->id,
        'dpr_number' => 'DPR-FILTER-APPROVED-01',
        'report_date' => now()->subDay()->toDateString(),
        'weather' => 'Clear',
        'skilled_workers' => 5,
        'unskilled_workers' => 10,
        'total_workers' => 15,
        'work_performed' => 'Excavation work',
        'status' => 'approved',
        'approved_by' => $pm->id,
        'approved_at' => now(),
    ]);

    // 1. Default should show submitted and hide approved
    $resDefault = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('dpr.index'));

    $resDefault->assertOk();
    $resDefault->assertSee('DPR-FILTER-SUBMITTED-01');
    $resDefault->assertDontSee('DPR-FILTER-APPROVED-01');

    // 2. Status = approved
    $resApproved = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('dpr.index', ['status' => 'approved']));

    $resApproved->assertOk();
    $resApproved->assertSee('DPR-FILTER-APPROVED-01');
    $resApproved->assertDontSee('DPR-FILTER-SUBMITTED-01');
});

test('client bills index defaults to uncertified bills and respects status filter', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();

    ClientBill::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'bill_number' => 'RA-FILTER-SUBMITTED-01',
        'bill_date' => now()->toDateString(),
        'period_start' => now()->startOfMonth()->toDateString(),
        'period_end' => now()->toDateString(),
        'gross_amount' => 50000,
        'net_payable' => 45000,
        'status' => 'submitted',
        'created_by' => $pm->id,
    ]);

    ClientBill::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'bill_number' => 'RA-FILTER-CERTIFIED-01',
        'bill_date' => now()->subMonth()->toDateString(),
        'period_start' => now()->subMonth()->startOfMonth()->toDateString(),
        'period_end' => now()->subMonth()->endOfMonth()->toDateString(),
        'gross_amount' => 100000,
        'net_payable' => 90000,
        'status' => 'certified',
        'created_by' => $pm->id,
        'certified_by' => $pm->id,
        'certified_at' => now(),
    ]);

    // 1. Default should show submitted and hide certified
    $resDefault = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('client-bills.index'));

    $resDefault->assertOk();
    $resDefault->assertSee('RA-FILTER-SUBMITTED-01');
    $resDefault->assertDontSee('RA-FILTER-CERTIFIED-01');

    // 2. Status = approved (certified)
    $resApproved = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('client-bills.index', ['status' => 'approved']));

    $resApproved->assertOk();
    $resApproved->assertSee('RA-FILTER-CERTIFIED-01');
    $resApproved->assertDontSee('RA-FILTER-SUBMITTED-01');
});

test('newly created material requisition is immediately visible on index without approval', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();
    $item = Item::first();

    // Store a new MR
    $storeRes = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('material-requisitions.store'), [
            'project_id' => $project->id,
            'requisition_no' => 'MR-IMMEDIATE-001',
            'requisition_date' => now()->toDateString(),
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity_requested' => 5,
                ],
            ],
        ]);

    $storeRes->assertRedirect();

    // Now visit index without any approval
    $indexRes = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('material-requisitions.index', ['status' => 'unapproved']));

    $indexRes->assertOk();
    $indexRes->assertSee('MR-IMMEDIATE-001');
    $indexRes->assertSee('Submitted');
});

