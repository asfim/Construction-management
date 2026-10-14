<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CostCode;
use App\Models\GoodsReceipt;
use App\Models\GrnItem;
use App\Models\Item;
use App\Models\MaterialRequisition;
use App\Models\MrItem;
use App\Models\PoItem;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\StockIssue;
use App\Models\StockIssueItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class ProcurementAndInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::where('code', 'HTP-CON')->first();
        $project1 = Project::where('code', 'PRJ-GLS-001')->first();
        if (! $company || ! $project1) {
            return;
        }

        $storekeeper = User::where('email', 'storekeeper@htech.com.bd')->first();
        $siteEngineer = User::where('email', 'site.engineer@htech.com.bd')->first();
        $pm = User::where('email', 'pm@htech.com.bd')->first();
        $procurement = User::where('email', 'procurement@htech.com.bd')->first();

        // 1. Warehouses
        $centralYard = Warehouse::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'WH-TEJ-01'],
            [
                'name' => 'Dhaka Central Yard & Warehouse (Tejgaon)',
                'location' => 'Tejgaon I/A, Dhaka-1208',
                'is_central' => true,
                'is_active' => true,
                'storekeeper_id' => $storekeeper?->id,
            ]
        );

        $siteStore = Warehouse::firstOrCreate(
            ['company_id' => $company->id, 'code' => 'WH-GLS-01'],
            [
                'project_id' => $project1->id,
                'name' => 'Gulshan Project Site Store',
                'location' => 'Plot 88, Road 17, Gulshan-2, Dhaka',
                'is_central' => false,
                'is_active' => true,
                'storekeeper_id' => $storekeeper?->id,
            ]
        );

        // 2. Material Requisition (MR)
        $rebarItem = Item::where('code', 'MAT-ROD-16MM')->first();
        $cementItem = Item::where('code', 'MAT-CEM-OPC')->first();
        $sandItem = Item::where('code', 'MAT-SAND-SYL')->first();
        $costCode = CostCode::where('code', '02-SUPER')->first();

        if ($rebarItem && $cementItem && $sandItem) {
            $mr = MaterialRequisition::firstOrCreate(
                ['company_id' => $company->id, 'requisition_no' => 'MR-GLS-001'],
                [
                    'project_id' => $project1->id,
                    'requisition_date' => '2025-02-01',
                    'required_by_date' => '2025-02-15',
                    'status' => 'approved',
                    'submitted_by' => $siteEngineer?->id,
                    'pm_approved_by' => $pm?->id,
                    'pm_approved_at' => '2025-02-02 11:00:00',
                    'approved_by' => $procurement?->id,
                    'approved_at' => '2025-02-03 15:30:00',
                    'notes' => 'Materials required for 2nd floor column casting and shear walls.',
                ]
            );

            $mrItem1 = MrItem::firstOrCreate(
                ['material_requisition_id' => $mr->id, 'item_id' => $rebarItem->id],
                [
                    'cost_code_id' => $costCode?->id,
                    'quantity_requested' => 25.0000,
                    'quantity_approved' => 25.0000,
                    'quantity_ordered' => 25.0000,
                    'remarks' => '16mm 500W rebar',
                ]
            );

            $mrItem2 = MrItem::firstOrCreate(
                ['material_requisition_id' => $mr->id, 'item_id' => $cementItem->id],
                [
                    'cost_code_id' => $costCode?->id,
                    'quantity_requested' => 500.0000,
                    'quantity_approved' => 500.0000,
                    'quantity_ordered' => 0.0000,
                    'remarks' => 'OPC Cement fresh delivery',
                ]
            );

            // 3. Purchase Order (PO) to BSRM
            $supplierBsrm = Supplier::where('code', 'SUP-BSRM')->first();
            if ($supplierBsrm) {
                $po = PurchaseOrder::firstOrCreate(
                    ['company_id' => $company->id, 'po_number' => 'PO-GLS-2025-001'],
                    [
                        'project_id' => $project1->id,
                        'supplier_id' => $supplierBsrm->id,
                        'po_date' => '2025-02-05',
                        'expected_delivery_date' => '2025-02-12',
                        'subtotal' => 2312500.00,
                        'vat_percentage' => 7.50,
                        'vat_amount' => 173437.50,
                        'total_amount' => 2485937.50,
                        'status' => 'partially_received',
                        'delivery_address' => 'Gulshan-2 Site, Plot 88, Road 17, Dhaka',
                        'payment_terms' => '45 Days Credit upon GRN inspection',
                        'created_by' => $procurement?->id,
                        'approved_by' => $pm?->id,
                        'approved_at' => '2025-02-06 10:00:00',
                        'notes' => 'Supply of 25 Ton BSRM 16mm 500W rebar.',
                    ]
                );

                $poItem = PoItem::firstOrCreate(
                    ['purchase_order_id' => $po->id, 'item_id' => $rebarItem->id],
                    [
                        'mr_item_id' => $mrItem1->id,
                        'quantity_ordered' => 25.0000,
                        'quantity_received' => 15.0000,
                        'unit_price' => 92500.00,
                        'total_amount' => 2312500.00,
                    ]
                );

                // 4. Goods Receipt Note (GRN) — Partial Delivery 15 Tons
                $grn = GoodsReceipt::firstOrCreate(
                    ['company_id' => $company->id, 'grn_number' => 'GRN-GLS-2025-001'],
                    [
                        'project_id' => $project1->id,
                        'purchase_order_id' => $po->id,
                        'warehouse_id' => $siteStore->id,
                        'received_date' => '2025-02-10',
                        'delivery_challan_no' => 'CH-BSRM-09821',
                        'supplier_invoice_no' => 'INV-BSRM-4412',
                        'status' => 'approved',
                        'received_by' => $storekeeper?->id,
                        'verified_by' => $pm?->id,
                        'verified_at' => '2025-02-10 16:00:00',
                        'notes' => '1st partial delivery received and tested.',
                    ]
                );

                $grnItem = GrnItem::firstOrCreate(
                    ['goods_receipt_id' => $grn->id, 'po_item_id' => $poItem->id],
                    [
                        'item_id' => $rebarItem->id,
                        'quantity_challan' => 15.0000,
                        'quantity_received' => 15.0000,
                        'quantity_accepted' => 15.0000,
                        'quantity_rejected' => 0.0000,
                        'unit_price' => 92500.00,
                        'remarks' => 'Weight bridge slip verified.',
                    ]
                );

                // Ensure initial inventory transaction exists for GRN
                $grn->warehouse->transactions()->firstOrCreate(
                    ['reference_type' => GoodsReceipt::class, 'reference_id' => $grn->id],
                    [
                        'company_id' => $company->id,
                        'project_id' => $project1->id,
                        'item_id' => $rebarItem->id,
                        'transaction_type' => 'grn_in',
                        'quantity' => 15.0000,
                        'unit_cost' => 92500.00,
                        'total_cost' => 1387500.00,
                        'transaction_date' => '2025-02-10',
                        'notes' => 'Challan #CH-BSRM-09821',
                        'created_by' => $storekeeper?->id,
                    ]
                );

                // 5. Stock Issue (SIN) — 5 Tons issued for Column Casting
                $sin = StockIssue::firstOrCreate(
                    ['company_id' => $company->id, 'issue_no' => 'SIN-GLS-2025-001'],
                    [
                        'project_id' => $project1->id,
                        'warehouse_id' => $siteStore->id,
                        'issue_date' => '2025-02-12',
                        'issued_to_contractor_or_labor' => 'Al-Amin Masonry Team (Bar Benders)',
                        'cost_code_id' => $costCode?->id,
                        'status' => 'approved',
                        'issued_by' => $storekeeper?->id,
                        'notes' => 'Rebar issued for 2nd floor column fabrication.',
                    ]
                );

                StockIssueItem::firstOrCreate(
                    ['stock_issue_id' => $sin->id, 'item_id' => $rebarItem->id],
                    [
                        'quantity_issued' => 5.0000,
                        'unit_cost' => 92500.00,
                        'total_cost' => 462500.00,
                    ]
                );

                $siteStore->transactions()->firstOrCreate(
                    ['reference_type' => StockIssue::class, 'reference_id' => $sin->id],
                    [
                        'company_id' => $company->id,
                        'project_id' => $project1->id,
                        'item_id' => $rebarItem->id,
                        'transaction_type' => 'issue_out',
                        'quantity' => -5.0000,
                        'unit_cost' => 92500.00,
                        'total_cost' => 462500.00,
                        'transaction_date' => '2025-02-12',
                        'notes' => 'Issued to Al-Amin Masonry Team',
                        'created_by' => $storekeeper?->id,
                    ]
                );
            }
        }
    }
}
