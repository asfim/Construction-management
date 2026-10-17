<?php

use App\Models\BoqItem;
use App\Models\Budget;
use App\Models\ClientBill;
use App\Models\Company;
use App\Models\CostCode;
use App\Models\DailyProgressReport;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\User;

test('site engineer can submit DPR and project manager can approve it', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $siteEngineer = User::where('email', 'site.engineer@htech.com.bd')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();
    $boqItem = BoqItem::whereHas('boqVersion', fn ($q) => $q->where('project_id', $project->id))->first();

    $response = $this->actingAs($siteEngineer)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('dpr.store'), [
            'project_id' => $project->id,
            'report_date' => now()->toDateString(),
            'weather' => 'Sunny',
            'skilled_workers' => 10,
            'unskilled_workers' => 20,
            'work_performed' => 'Excavation completed for Grid 1 to 4.',
            'equipment_deployed' => '1 Excavator, 2 Dump Trucks',
            'activities' => [
                [
                    'boq_item_id' => $boqItem?->id,
                    'activity_name' => 'Foundation Pit Excavation',
                    'location' => 'Grid 1-4',
                    'quantity_completed' => 1500.0000,
                    'unit' => 'cft',
                ],
            ],
        ]);

    $response->assertRedirect();
    $dpr = DailyProgressReport::where('project_id', $project->id)->latest('id')->first();
    expect($dpr)->not->toBeNull();
    expect($dpr->total_workers)->toBe(30);
    expect($dpr->status)->toBe('submitted');

    // Site engineer can view DPR details page
    $showResponse = $this->actingAs($siteEngineer)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('dpr.show', $dpr));
    $showResponse->assertOk();
    $showResponse->assertSee($dpr->dpr_number);
    $showResponse->assertSee($dpr->report_date->format('d M, Y'));

    // PM approves the DPR
    $approveResponse = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('dpr.approve', $dpr));
    $approveResponse->assertRedirect();

    $dpr->refresh();
    expect($dpr->status)->toBe('approved');
    expect($dpr->approved_by)->toBe($pm->id);
});

test('qs can create running account RA bill with advance recovery and retention deductions', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $qs = User::where('email', 'qs@htech.com.bd')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();
    $boqItem = BoqItem::whereHas('boqVersion', fn ($q) => $q->where('project_id', $project->id))->first();

    // Quantity 2000 cft * rate 45 = 90,000 gross
    // Advance recovery 10% = 9,000
    // Retention 5% = 4,500
    // VAT 7.5% = 6,750
    // Net = 90,000 - 9,000 - 4,500 + 6,750 = 83,250
    $response = $this->actingAs($qs)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('client-bills.store'), [
            'project_id' => $project->id,
            'bill_number' => 'RA-TEST-002',
            'bill_date' => now()->toDateString(),
            'period_start' => now()->subMonth()->toDateString(),
            'period_end' => now()->toDateString(),
            'advance_recovery_percentage' => 10.00,
            'retention_percentage' => 5.00,
            'vat_percentage' => 7.50,
            'items' => [
                [
                    'boq_item_id' => $boqItem->id,
                    'unit_rate' => 45.00,
                    'previous_quantity' => 0,
                    'current_quantity' => 2000.0000,
                ],
            ],
        ]);

    $response->assertRedirect();
    $bill = ClientBill::where('bill_number', 'RA-TEST-002')->first();
    expect($bill)->not->toBeNull();
    expect((float) $bill->gross_amount)->toBe(90000.00);
    expect((float) $bill->advance_recovery_amount)->toBe(9000.00);
    expect((float) $bill->retention_amount)->toBe(4500.00);
    expect((float) $bill->vat_amount)->toBe(6750.00);
    expect((float) $bill->net_payable)->toBe(83250.00);
    expect($bill->status)->toBe('submitted');

    // PM certifies bill
    $certifyResponse = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('client-bills.certify', $bill));
    $certifyResponse->assertRedirect();

    $bill->refresh();
    expect($bill->status)->toBe('certified');
    expect($bill->certified_by)->toBe($pm->id);
});

test('project expense voucher updates budget line actual expenditure', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();
    $project = Project::first();
    $costCode = CostCode::where('company_id', $company->id)->first();

    // Ensure budget and budget line exist
    $budget = Budget::firstOrCreate(
        ['company_id' => $company->id, 'project_id' => $project->id],
        ['budget_code' => 'BDG-TEST-01', 'title' => 'Master Budget', 'status' => 'approved']
    );

    $budgetLine = $budget->lines()->firstOrCreate(
        ['cost_code_id' => $costCode->id],
        ['planned_amount' => 500000.00, 'committed_amount' => 0.00, 'actual_amount' => 50000.00, 'variance' => 450000.00]
    );

    $initialActual = (float) $budgetLine->actual_amount;

    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('project-expenses.store'), [
            'project_id' => $project->id,
            'cost_code_id' => $costCode->id,
            'voucher_number' => 'EXP-TEST-001',
            'expense_date' => now()->toDateString(),
            'payee_name' => 'Meghna Petroleum Fuel Station',
            'payment_method' => 'cash',
            'amount' => 15000.00,
            'description' => 'Diesel fuel for excavator and site generator.',
        ]);

    $response->assertRedirect(route('project-expenses.index'));

    $expense = ProjectExpense::where('voucher_number', 'EXP-TEST-001')->first();
    expect($expense)->not->toBeNull();
    expect((float) $expense->amount)->toBe(15000.00);

    // Budget line actual_amount must be incremented by 15,000
    $budgetLine->refresh();
    expect((float) $budgetLine->actual_amount)->toBe($initialActual + 15000.00);
});
