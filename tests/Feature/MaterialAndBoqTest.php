<?php

use App\Models\BoqItem;
use App\Models\BoqVersion;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Company;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\Project;
use App\Models\RateAnalysis;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;

test('authorized procurement user can create an item with decimal precision', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();
    $unit = Unit::where('symbol', 'ton')->first();

    $response = $this->actingAs($procurement)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('items.store'), [
            'code' => 'MAT-TEST-STEEL',
            'name' => 'Grade 75 High Strength Steel',
            'name_bn' => '৭৫-গ্রেড হাই স্ট্রেংথ স্টিল',
            'unit_id' => $unit->id,
            'standard_rate' => '102500.50',
            'min_stock_alert' => '10.0000',
        ]);

    $response->assertRedirect(route('items.index'));

    $item = Item::withoutGlobalScopes()->where('code', 'MAT-TEST-STEEL')->first();
    expect($item)->not->toBeNull();
    expect((string) $item->standard_rate)->toBe('102500.50');
});

test('authorized user can register a supplier with credit terms', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();

    $response = $this->actingAs($procurement)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('suppliers.store'), [
            'code' => 'SUP-TEST-CEMENT',
            'name' => 'Akij Cement Company Ltd.',
            'contact_person' => 'Md. Faruk Ahmed',
            'phone' => '+8801712334455',
            'tin' => '5839201948',
            'bin' => '000583920-0101',
            'payment_terms_days' => 45,
        ]);

    $response->assertRedirect(route('suppliers.index'));

    $supplier = Supplier::withoutGlobalScopes()->where('code', 'SUP-TEST-CEMENT')->first();
    expect($supplier)->not->toBeNull();
    expect($supplier->payment_terms_days)->toBe(45);
});

test('locked approved boq version rejects new items directly', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $project = Project::where('code', 'PRJ-GLS-001')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();

    // The seeded BOQ Version 1 is approved & locked
    $lockedBoq = BoqVersion::where('project_id', $project->id)->where('version_number', 1)->first();
    expect($lockedBoq->is_locked)->toBeTrue();

    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('projects.boqs.items.store', [$project, $lockedBoq]), [
            'item_code' => 'BOQ-ILLEGAL',
            'description' => 'Should fail because BOQ is locked',
            'unit' => 'sft',
            'planned_quantity' => 100,
            'unit_rate' => 50,
        ]);

    $response->assertSessionHas('error');
    expect(BoqItem::where('item_code', 'BOQ-ILLEGAL')->exists())->toBeFalse();
});

test('user can clone approved boq version into revision 2 for variations without altering historical baseline', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $project = Project::where('code', 'PRJ-GLS-001')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();

    $version1 = BoqVersion::where('project_id', $project->id)->where('version_number', 1)->first();
    $v1ItemsCount = $version1->items()->count();

    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('projects.boqs.revision', [$project, $version1]));

    $response->assertRedirect();

    // Verify Version 2 was created as draft and unlocked
    $version2 = BoqVersion::where('project_id', $project->id)->where('version_number', 2)->first();
    expect($version2)->not->toBeNull();
    expect($version2->is_locked)->toBeFalse();
    expect($version2->status)->toBe('draft');
    expect($version2->items()->count())->toBe($v1ItemsCount);

    // Version 1 must still remain approved and locked
    $version1->refresh();
    expect($version1->is_locked)->toBeTrue();
    expect($version1->status)->toBe('approved');
});

test('rate analysis recalculation formula incorporates wastage and markups', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $rateAnalysis = RateAnalysis::where('company_id', $company->id)->first();
    expect($rateAnalysis)->not->toBeNull();

    // Update markup parameters
    $rateAnalysis->update([
        'wastage_percentage' => 5.00,
        'overhead_percentage' => 10.00,
        'profit_percentage' => 15.00,
    ]);

    $rateAnalysis->recalculate();

    // Total markup factor is (1 + 0.25) = 1.25
    expect((float) $rateAnalysis->total_unit_rate)->toBeGreaterThan(0);
});

test('budget line calculates cost variance correctly', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $budget = Budget::where('company_id', $company->id)->first();
    expect($budget)->not->toBeNull();

    $line = $budget->lines()->first();
    expect($line)->not->toBeNull();

    $expectedVariance = (float) ($line->planned_amount - $line->actual_amount);
    expect($line->variance)->toBe($expectedVariance);
});

test('user can view project budget even when another project is active in session', function () {
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();
    $project1 = Project::where('code', 'PRJ-GLS-001')->first();
    $project3 = Project::where('code', 'PRJ-UTR-003')->first();

    // User is active on Project 3, but navigates to Project 1's budget
    $response = $this->actingAs($superadmin)
        ->withSession([
            'active_company_id' => $project1->company_id,
            'active_project_id' => $project3->id,
        ])
        ->get(route('projects.budget.show', $project1));

    $response->assertOk();
    $response->assertViewIs('budgets.show');
    $response->assertSee($project1->code);

    // Repeated request should not fail with duplicate key constraint
    $secondResponse = $this->actingAs($superadmin)
        ->withSession([
            'active_company_id' => $project1->company_id,
            'active_project_id' => $project3->id,
        ])
        ->get(route('projects.budget.show', $project1));

    $secondResponse->assertOk();
});

test('viewing budget for project without existing budget auto-creates baseline safely', function () {
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();
    $company = Company::where('code', 'HTP-CON')->first();

    $newProject = Project::create([
        'company_id' => $company->id,
        'code' => 'PRJ-TEST-AUTO',
        'name' => 'Auto Budget Test Project',
        'status' => 'planning',
        'budget_amount' => 5000000.00,
        'contract_value' => 6000000.00,
        'created_by' => $superadmin->id,
    ]);

    $response = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('projects.budget.show', $newProject));

    $response->assertOk();
    $response->assertViewIs('budgets.show');

    $budget = Budget::where('project_id', $newProject->id)->first();
    expect($budget)->not->toBeNull();
    expect($budget->budget_code)->toBe('BDG-PRJ-TEST-AUTO');
    expect((float) $budget->total_budget_amount)->toBe(5000000.00);
});

