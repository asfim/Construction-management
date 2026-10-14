<?php

namespace Database\Seeders;

use App\Models\BoqItem;
use App\Models\ClientBill;
use App\Models\Company;
use App\Models\CostCode;
use App\Models\DailyProgressReport;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;
use Illuminate\Database\Seeder;

class SiteExecutionAndBillingSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('code', 'HTP-CON')->first();
        $project = Project::where('code', 'PRJ-GLS-001')->first();

        if (! $company || ! $project) {
            return;
        }

        $siteEngineer = User::where('email', 'site.engineer@htech.com.bd')->first();
        $pm = User::where('email', 'pm@htech.com.bd')->first();
        $qs = User::where('email', 'qs@htech.com.bd')->first();

        // 1. Seed Daily Progress Report
        $dpr = DailyProgressReport::firstOrCreate(
            ['company_id' => $company->id, 'dpr_number' => 'DPR-20261014-001'],
            [
                'project_id' => $project->id,
                'report_date' => now()->subDay()->toDateString(),
                'weather' => 'Sunny',
                'site_engineer_id' => $siteEngineer?->id ?? $pm->id,
                'skilled_workers' => 14,
                'unskilled_workers' => 28,
                'total_workers' => 42,
                'work_performed' => "Completed 5th-floor shear wall reinforcement binding and shuttering check. Commenced concrete pouring for grid B1 to D4 columns.",
                'equipment_deployed' => '1 Tower Crane, 2 Concrete Mixers, 4 Needle Vibrators, 1 Total Station',
                'material_received_used' => 'Consumed 180 bags Seven Rings OPC cement and 4.2 tons 20mm BSRM rebar.',
                'issues_safety_notes' => 'Mandatory morning toolbox talk conducted on fall protection harness usage. Zero safety incidents.',
                'status' => 'approved',
                'approved_by' => $pm->id,
                'approved_at' => now(),
            ]
        );

        $boqItem = BoqItem::whereHas('boqVersion', fn ($q) => $q->where('project_id', $project->id))->first();

        if ($dpr->activities()->count() === 0) {
            $dpr->activities()->create([
                'boq_item_id' => $boqItem?->id,
                'activity_name' => 'RCC Column Concrete Casting & Curing',
                'location' => '5th Floor, Grid B1-D4',
                'quantity_completed' => 450.0000,
                'unit' => 'cft',
                'remarks' => 'Slump test checked at 100mm, 6 test cylinders sampled.',
            ]);

            $dpr->activities()->create([
                'boq_item_id' => null,
                'activity_name' => 'Steel Rebar Binding & Spacer Block Placement',
                'location' => '5th Floor Core Shear Walls',
                'quantity_completed' => 3.8000,
                'unit' => 'ton',
                'remarks' => 'Cover block clearance verified by QA inspector.',
            ]);
        }

        // 2. Seed Client Running Account (RA) Bill
        $bill = ClientBill::firstOrCreate(
            ['company_id' => $company->id, 'bill_number' => 'RA-01'],
            [
                'project_id' => $project->id,
                'bill_date' => now()->subDays(5)->toDateString(),
                'period_start' => now()->subMonth()->toDateString(),
                'period_end' => now()->subDays(5)->toDateString(),
                'gross_amount' => 1425000.00,
                'advance_recovery_percentage' => 10.00,
                'advance_recovery_amount' => 142500.00,
                'retention_percentage' => 5.00,
                'retention_amount' => 71250.00,
                'vat_percentage' => 7.50,
                'vat_amount' => 106875.00,
                'net_payable' => 1318125.00,
                'status' => 'certified',
                'created_by' => $qs?->id ?? $pm->id,
                'certified_by' => $pm->id,
                'certified_at' => now()->subDays(2),
                'notes' => '1st Running Account Bill covering substructure excavation, mat foundation casting, and basement-1 retaining wall works.',
            ]
        );

        if ($bill->items()->count() === 0 && $boqItem) {
            $bill->items()->create([
                'boq_item_id' => $boqItem->id,
                'description' => $boqItem->description,
                'unit' => $boqItem->unit,
                'unit_rate' => $boqItem->unit_rate,
                'previous_quantity' => 0,
                'current_quantity' => 2500.0000,
                'total_quantity' => 2500.0000,
                'current_amount' => 1425000.00,
                'remarks' => 'Certified as per Measurement Book (MB) volume-1, page 14-19.',
            ]);
        }

        // 3. Seed Project Expenses
        $costCodeSub = CostCode::where('code', '01-SUB')->first() ?? CostCode::first();

        ProjectExpense::firstOrCreate(
            ['company_id' => $company->id, 'voucher_number' => 'EXP-202610-001'],
            [
                'project_id' => $project->id,
                'cost_code_id' => $costCodeSub->id,
                'expense_date' => now()->subDays(3)->toDateString(),
                'payee_name' => 'Padma Oil Distributor',
                'payment_method' => 'bank_transfer',
                'amount' => 65000.00,
                'description' => 'Diesel fuel procurement (500 Liters) for on-site concrete pump and backup power generator.',
                'reference_no' => 'INV-POL-9921',
                'status' => 'paid',
                'created_by' => $pm->id,
                'approved_by' => $pm->id,
                'approved_at' => now()->subDays(3),
            ]
        );

        ProjectExpense::firstOrCreate(
            ['company_id' => $company->id, 'voucher_number' => 'EXP-202610-002'],
            [
                'project_id' => $project->id,
                'cost_code_id' => $costCodeSub->id,
                'expense_date' => now()->subDay()->toDateString(),
                'payee_name' => 'Dhaka North City Corporation (DNCC)',
                'payment_method' => 'cheque',
                'amount' => 25000.00,
                'description' => 'Road cutting and site temporary barricade municipal utility inspection fee.',
                'reference_no' => 'DNCC-CHQ-4401',
                'status' => 'paid',
                'created_by' => $pm->id,
                'approved_by' => $pm->id,
                'approved_at' => now()->subDay(),
            ]
        );
    }
}
