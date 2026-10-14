<?php

namespace Database\Seeders;

use App\Models\BoqItem;
use App\Models\BoqVersion;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Company;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Project;
use App\Models\RateAnalysis;
use App\Models\RateAnalysisComponent;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaterialAndBoqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::where('code', 'HTP-CON')->first();
        if (! $company) {
            return;
        }

        // 1. Units
        $units = [
            ['name' => 'Bag', 'name_bn' => 'Bag', 'symbol' => 'bag', 'type' => 'unit'],
            ['name' => 'Ton', 'name_bn' => 'Metric Ton', 'symbol' => 'ton', 'type' => 'weight'],
            ['name' => 'Kilogram', 'name_bn' => 'Kilogram', 'symbol' => 'kg', 'type' => 'weight'],
            ['name' => 'Cubic Feet', 'name_bn' => 'Cubic Feet', 'symbol' => 'cft', 'type' => 'volume'],
            ['name' => 'Cubic Meter', 'name_bn' => 'Cubic Meter', 'symbol' => 'cum', 'type' => 'volume'],
            ['name' => 'Square Feet', 'name_bn' => 'Square Feet', 'symbol' => 'sft', 'type' => 'area'],
            ['name' => 'Running Feet', 'name_bn' => 'Running Feet', 'symbol' => 'rft', 'type' => 'length'],
            ['name' => 'Number', 'name_bn' => 'Number', 'symbol' => 'nos', 'type' => 'count'],
        ];

        $unitMap = [];
        foreach ($units as $u) {
            $unitMap[$u['symbol']] = Unit::firstOrCreate(
                ['company_id' => $company->id, 'symbol' => $u['symbol']],
                ['name' => $u['name'], 'name_bn' => $u['name_bn'], 'type' => $u['type'], 'is_active' => true]
            );
        }

        // Conversions
        if (isset($unitMap['ton'], $unitMap['kg'])) {
            UnitConversion::firstOrCreate(
                ['company_id' => $company->id, 'from_unit_id' => $unitMap['ton']->id, 'to_unit_id' => $unitMap['kg']->id],
                ['conversion_factor' => 1000.000000]
            );
        }
        if (isset($unitMap['bag'], $unitMap['kg'])) {
            UnitConversion::firstOrCreate(
                ['company_id' => $company->id, 'from_unit_id' => $unitMap['bag']->id, 'to_unit_id' => $unitMap['kg']->id],
                ['conversion_factor' => 50.000000]
            );
        }
        if (isset($unitMap['cum'], $unitMap['cft'])) {
            UnitConversion::firstOrCreate(
                ['company_id' => $company->id, 'from_unit_id' => $unitMap['cum']->id, 'to_unit_id' => $unitMap['cft']->id],
                ['conversion_factor' => 35.314700]
            );
        }

        // 2. Categories
        $categories = [
            ['code' => 'CAT-REBAR', 'name' => 'Steel & Rebar', 'name_bn' => 'Steel & Rebar'],
            ['code' => 'CAT-CEMENT', 'name' => 'Cement & Binders', 'name_bn' => 'Cement & Binders'],
            ['code' => 'CAT-AGGREGATE', 'name' => 'Sand & Aggregates', 'name_bn' => 'Sand & Aggregates'],
            ['code' => 'CAT-BRICK', 'name' => 'Bricks & Blocks', 'name_bn' => 'Bricks & Blocks'],
            ['code' => 'CAT-FINISH', 'name' => 'Finishing Materials', 'name_bn' => 'Finishing Materials'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['code']] = ItemCategory::firstOrCreate(
                ['company_id' => $company->id, 'code' => $cat['code']],
                ['name' => $cat['name'], 'name_bn' => $cat['name_bn'], 'is_active' => true]
            );
        }

        // 3. Materials / Items
        $items = [
            [
                'code' => 'MAT-ROD-16MM',
                'name' => '60-Grade Deformed Rebar 16mm',
                'name_bn' => '16mm 60-Grade Deformed Rebar',
                'category_id' => $catMap['CAT-REBAR']->id,
                'unit_id' => $unitMap['ton']->id,
                'specification' => 'BDS ISO 6935-2 / 500W Grade',
                'brand' => 'BSRM Steels',
                'standard_rate' => 92500.00,
                'min_stock_alert' => 5.0000,
            ],
            [
                'code' => 'MAT-ROD-20MM',
                'name' => '60-Grade Deformed Rebar 20mm',
                'name_bn' => '20mm 60-Grade Deformed Rebar',
                'category_id' => $catMap['CAT-REBAR']->id,
                'unit_id' => $unitMap['ton']->id,
                'specification' => 'BDS ISO 6935-2 / 500W Grade',
                'brand' => 'BSRM Steels',
                'standard_rate' => 92500.00,
                'min_stock_alert' => 5.0000,
            ],
            [
                'code' => 'MAT-CEM-OPC',
                'name' => 'Ordinary Portland Cement (OPC)',
                'name_bn' => 'Ordinary Portland Cement (OPC)',
                'category_id' => $catMap['CAT-CEMENT']->id,
                'unit_id' => $unitMap['bag']->id,
                'specification' => 'BDS EN 197-1 CEM I 42.5N',
                'brand' => 'Seven Rings Cement',
                'standard_rate' => 540.00,
                'min_stock_alert' => 100.0000,
            ],
            [
                'code' => 'MAT-SAND-SYL',
                'name' => 'Sylhet Coarse Sand (2.5 FM)',
                'name_bn' => 'Sylhet Coarse Sand (2.5 FM)',
                'category_id' => $catMap['CAT-AGGREGATE']->id,
                'unit_id' => $unitMap['cft']->id,
                'specification' => 'Coarse Aggregate FM 2.5 - 2.8',
                'brand' => 'Local Sylhet',
                'standard_rate' => 45.00,
                'min_stock_alert' => 500.0000,
            ],
            [
                'code' => 'MAT-STONE-CHIP',
                'name' => '3/4" Down Stone Chips',
                'name_bn' => '3/4" Down Stone Chips',
                'category_id' => $catMap['CAT-AGGREGATE']->id,
                'unit_id' => $unitMap['cft']->id,
                'specification' => 'Graded Crushed Boulder Stone 19mm Down',
                'brand' => 'Bholaganj Black Stone',
                'standard_rate' => 210.00,
                'min_stock_alert' => 500.0000,
            ],
            [
                'code' => 'MAT-BRICK-1ST',
                'name' => '1st Class Machine-Made Bricks',
                'name_bn' => '1st Class Machine-Made Bricks',
                'category_id' => $catMap['CAT-BRICK']->id,
                'unit_id' => $unitMap['nos']->id,
                'specification' => 'Compressive Strength >= 2000 psi',
                'brand' => 'Mir Bricks',
                'standard_rate' => 12.50,
                'min_stock_alert' => 2000.0000,
            ],
        ];

        $itemMap = [];
        foreach ($items as $itemData) {
            $itemMap[$itemData['code']] = Item::firstOrCreate(
                ['company_id' => $company->id, 'code' => $itemData['code']],
                $itemData
            );
        }

        // 4. Verified Bangladesh Suppliers
        $suppliers = [
            [
                'code' => 'SUP-BSRM',
                'name' => 'BSRM Steels Limited',
                'contact_person' => 'Md. Anwarul Kabir, Senior Manager',
                'phone' => '+8801713000111',
                'email' => 'sales@bsrm.com',
                'address' => 'Ali Mansion, 1207/1099 Sadarghat Road, Chattogram / Dhaka Office: Mohakhali',
                'city' => 'Chattogram',
                'tin' => '182930491820',
                'bin' => '000182940-0102',
                'trade_license' => 'TRAD/CCC/019283/2023',
                'bank_name' => 'Eastern Bank PLC',
                'bank_account_no' => '1041060000123',
                'bank_branch' => 'Principal Branch, Dhaka',
                'payment_terms_days' => 45,
            ],
            [
                'code' => 'SUP-7RINGS',
                'name' => 'Seven Rings Cement (Shun Shing Group)',
                'contact_person' => 'Khandaker Moniruzzaman',
                'phone' => '+8801713000222',
                'email' => 'corporate@sevenrings.com',
                'address' => 'Land View Commercial Center, 28 Gulshan North C/A, Dhaka-1212',
                'city' => 'Dhaka',
                'tin' => '394820194820',
                'bin' => '000392819-0101',
                'trade_license' => 'TRAD/DNCC/082736/2023',
                'bank_name' => 'BRAC Bank PLC',
                'bank_account_no' => '1501200000456',
                'bank_branch' => 'Gulshan Branch, Dhaka',
                'payment_terms_days' => 30,
            ],
            [
                'code' => 'SUP-MIR',
                'name' => 'Mir Ceramic & Bricks Ltd.',
                'contact_person' => 'Engr. Nazmul Huda',
                'phone' => '+8801713000333',
                'email' => 'sales@mirceramic.com',
                'address' => 'House 14, Road 12, Dhanmondi, Dhaka',
                'city' => 'Dhaka',
                'tin' => '492819384910',
                'bin' => '000482910-0103',
                'trade_license' => 'TRAD/DSCC/072635/2024',
                'bank_name' => 'City Bank PLC',
                'bank_account_no' => '1101890000789',
                'bank_branch' => 'Dhanmondi Branch',
                'payment_terms_days' => 30,
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(
                ['company_id' => $company->id, 'code' => $sup['code']],
                $sup
            );
        }

        // 5. Cost Codes
        $costCodes = [
            ['code' => '01-SUB', 'name' => 'Substructure & Foundation', 'name_bn' => 'Substructure & Foundation'],
            ['code' => '02-SUPER', 'name' => 'Superstructure RCC Frame', 'name_bn' => 'Superstructure RCC Frame'],
            ['code' => '03-BRICK', 'name' => 'Brickwork & Masonry', 'name_bn' => 'Brickwork & Masonry'],
            ['code' => '04-PLASTER', 'name' => 'Plaster & Surface Finishing', 'name_bn' => 'Plaster & Surface Finishing'],
            ['code' => '05-MEP', 'name' => 'MEP & Utilities', 'name_bn' => 'MEP & Utilities'],
        ];

        $codeMap = [];
        foreach ($costCodes as $cc) {
            $codeMap[$cc['code']] = CostCode::firstOrCreate(
                ['company_id' => $company->id, 'code' => $cc['code']],
                ['name' => $cc['name'], 'name_bn' => $cc['name_bn'], 'is_active' => true]
            );
        }

        // 6. Realistic BOQ for Project 1 ("Gulshan Commercial Tower")
        $project1 = Project::where('code', 'PRJ-GLS-001')->first();
        $pm = User::where('email', 'pm@htech.com.bd')->first();
        $owner = User::where('email', 'owner@htech.com.bd')->first();

        if ($project1) {
            $boqVersion = BoqVersion::firstOrCreate(
                ['project_id' => $project1->id, 'version_number' => 1],
                [
                    'company_id' => $company->id,
                    'title' => 'Original Approved Baseline BOQ',
                    'total_amount' => 91265000.00,
                    'status' => 'approved',
                    'is_locked' => true,
                    'created_by' => $pm?->id,
                    'approved_by' => $owner?->id,
                    'approved_at' => '2025-01-20 10:00:00',
                    'remarks' => 'Initial approved contract baseline. Cannot be modified without variation order.',
                ]
            );

            // Add BOQ Items
            $boqItemsData = [
                [
                    'cost_code_id' => $codeMap['01-SUB']->id,
                    'item_code' => 'BOQ-01.01',
                    'description' => 'Cast-in-situ reinforced concrete bored piling 500mm diameter including boring, temporary casing, reinforcement cage and tremie pipe pouring.',
                    'unit' => 'rft',
                    'planned_quantity' => 12000.0000,
                    'unit_rate' => 1450.00,
                    'total_amount' => 17400000.00,
                    'sort_order' => 1,
                ],
                [
                    'cost_code_id' => $codeMap['02-SUPER']->id,
                    'item_code' => 'BOQ-02.01',
                    'description' => 'Cast-in-situ RCC 1:1.5:3 with Sylhet sand & crushed stone chips in columns, shear walls, beams and 150mm thick roof slabs including shuttering.',
                    'unit' => 'cft',
                    'planned_quantity' => 45000.0000,
                    'unit_rate' => 480.00,
                    'total_amount' => 21600000.00,
                    'sort_order' => 2,
                ],
                [
                    'cost_code_id' => $codeMap['02-SUPER']->id,
                    'item_code' => 'BOQ-02.02',
                    'description' => 'Supplying, bending, binding in position 500W / 60-Grade high-strength deformed rebar.',
                    'unit' => 'ton',
                    'planned_quantity' => 350.0000,
                    'unit_rate' => 108000.00,
                    'total_amount' => 37800000.00,
                    'sort_order' => 3,
                ],
                [
                    'cost_code_id' => $codeMap['03-BRICK']->id,
                    'item_code' => 'BOQ-03.01',
                    'description' => '125mm thick 1st class brickwork in 1:4 cement-sand mortar in all floor partitions including curing and scaffolding.',
                    'unit' => 'sft',
                    'planned_quantity' => 65000.0000,
                    'unit_rate' => 145.00,
                    'total_amount' => 9425000.00,
                    'sort_order' => 4,
                ],
                [
                    'cost_code_id' => $codeMap['04-PLASTER']->id,
                    'item_code' => 'BOQ-04.01',
                    'description' => '12mm thick cement-sand plaster (1:4) to walls and ceiling including smooth finishing and 14 days water curing.',
                    'unit' => 'sft',
                    'planned_quantity' => 120000.0000,
                    'unit_rate' => 42.00,
                    'total_amount' => 5040000.00,
                    'sort_order' => 5,
                ],
            ];

            $savedBoqItems = [];
            foreach ($boqItemsData as $itemRow) {
                $savedBoqItems[$itemRow['item_code']] = BoqItem::firstOrCreate(
                    ['boq_version_id' => $boqVersion->id, 'item_code' => $itemRow['item_code']],
                    array_merge($itemRow, ['project_id' => $project1->id])
                );
            }

            // 7. Rate Analysis for BOQ-02.01 (RCC 1:1.5:3 per 100 cft)
            $rccItem = $savedBoqItems['BOQ-02.01'] ?? null;
            if ($rccItem) {
                $rateAnalysis = RateAnalysis::firstOrCreate(
                    ['boq_item_id' => $rccItem->id],
                    [
                        'company_id' => $company->id,
                        'title' => 'Rate Analysis for 100 CFT RCC 1:1.5:3 with Stone Chips',
                        'unit' => 'cft',
                        'material_cost' => 320.00,
                        'labor_cost' => 75.00,
                        'equipment_cost' => 15.00,
                        'overhead_percentage' => 5.00,
                        'profit_percentage' => 10.00,
                        'wastage_percentage' => 3.00,
                        'total_unit_rate' => 480.00,
                        'status' => 'approved',
                        'created_by' => $pm?->id,
                        'approved_by' => $owner?->id,
                    ]
                );

                // Add Components
                $components = [
                    ['component_type' => 'material', 'description' => 'Cement (Seven Rings OPC)', 'unit' => 'bag', 'quantity' => 0.2200, 'rate' => 540.00, 'amount' => 118.80],
                    ['component_type' => 'material', 'description' => 'Sylhet Coarse Sand (2.5 FM)', 'unit' => 'cft', 'quantity' => 0.4400, 'rate' => 45.00, 'amount' => 19.80],
                    ['component_type' => 'material', 'description' => 'Stone Chips 3/4" Down', 'unit' => 'cft', 'quantity' => 0.8800, 'rate' => 210.00, 'amount' => 184.80],
                    ['component_type' => 'labor', 'description' => 'Head Mason & Masons', 'unit' => 'day', 'quantity' => 0.0400, 'rate' => 1000.00, 'amount' => 40.00],
                    ['component_type' => 'labor', 'description' => 'Helpers / Unskilled Labor', 'unit' => 'day', 'quantity' => 0.0500, 'rate' => 700.00, 'amount' => 35.00],
                    ['component_type' => 'equipment', 'description' => 'Concrete Mixture Machine & Vibrator run', 'unit' => 'cft', 'quantity' => 1.0000, 'rate' => 15.00, 'amount' => 15.00],
                ];

                foreach ($components as $comp) {
                    RateAnalysisComponent::firstOrCreate(
                        ['rate_analysis_id' => $rateAnalysis->id, 'description' => $comp['description']],
                        $comp
                    );
                }
            }

            // 8. Project Approved Budget
            $budget = Budget::firstOrCreate(
                ['project_id' => $project1->id, 'budget_code' => 'BDG-GLS-001'],
                [
                    'company_id' => $company->id,
                    'boq_version_id' => $boqVersion->id,
                    'title' => 'Approved Internal Project Execution Budget',
                    'total_budget_amount' => 78500000.00,
                    'contingency_amount' => 3000000.00,
                    'status' => 'approved',
                    'created_by' => $pm?->id,
                    'approved_by' => $owner?->id,
                    'approved_at' => '2025-01-22 14:00:00',
                    'remarks' => 'Internal cost baseline controlling POs and subcontract work orders.',
                ]
            );

            // Budget Lines by Cost Code
            $budgetLinesData = [
                ['cost_code_id' => $codeMap['01-SUB']->id, 'planned_amount' => 15000000.00, 'committed_amount' => 8500000.00, 'actual_amount' => 7200000.00, 'forecast_amount' => 14800000.00],
                ['cost_code_id' => $codeMap['02-SUPER']->id, 'planned_amount' => 51000000.00, 'committed_amount' => 22000000.00, 'actual_amount' => 18500000.00, 'forecast_amount' => 50500000.00],
                ['cost_code_id' => $codeMap['03-BRICK']->id, 'planned_amount' => 7500000.00, 'committed_amount' => 1200000.00, 'actual_amount' => 950000.00, 'forecast_amount' => 7500000.00],
                ['cost_code_id' => $codeMap['04-PLASTER']->id, 'planned_amount' => 5000000.00, 'committed_amount' => 0.00, 'actual_amount' => 0.00, 'forecast_amount' => 5000000.00],
            ];

            foreach ($budgetLinesData as $bLine) {
                BudgetLine::firstOrCreate(
                    ['budget_id' => $budget->id, 'cost_code_id' => $bLine['cost_code_id']],
                    $bLine
                );
            }
        }
    }
}
