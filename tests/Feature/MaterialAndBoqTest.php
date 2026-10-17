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

test('authorized user can delete a draft boq line item and recalculates boq total', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $project = Project::where('code', 'PRJ-GLS-001')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();

    $draftBoq = BoqVersion::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'version_number' => 99,
        'title' => 'Temporary Test BOQ',
        'status' => 'draft',
        'is_locked' => false,
        'created_by' => $pm->id,
        'total_amount' => 0,
    ]);

    $item1 = BoqItem::create([
        'boq_version_id' => $draftBoq->id,
        'project_id' => $project->id,
        'item_code' => 'TEST-001',
        'description' => 'Test Item 1',
        'unit' => 'cft',
        'planned_quantity' => 10,
        'unit_rate' => 100,
    ]);

    $item2 = BoqItem::create([
        'boq_version_id' => $draftBoq->id,
        'project_id' => $project->id,
        'item_code' => 'TEST-002',
        'description' => 'Test Item 2',
        'unit' => 'cft',
        'planned_quantity' => 5,
        'unit_rate' => 200,
    ]);

    $draftBoq->refresh();
    expect((float) $draftBoq->total_amount)->toBe(2000.00);

    // Delete item 1
    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->delete(route('projects.boqs.items.destroy', [$project, $draftBoq, $item1]));

    $response->assertRedirect();
    expect(BoqItem::find($item1->id))->toBeNull();

    $draftBoq->refresh();
    expect((float) $draftBoq->total_amount)->toBe(1000.00);

    // Now delete the entire draft BOQ version
    $delResponse = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->delete(route('projects.boqs.destroy', [$project, $draftBoq]));

    $delResponse->assertRedirect(route('projects.boqs.index', $project));
    expect(BoqVersion::find($draftBoq->id))->toBeNull();
    expect(BoqItem::find($item2->id))->toBeNull(); // cascading delete
});

test('authorized user can update boq version details and line items, and unlock locked boq', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $project = Project::where('code', 'PRJ-GLS-001')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();

    $boq = BoqVersion::create([
        'company_id' => $company->id,
        'project_id' => $project->id,
        'version_number' => 88,
        'title' => 'Initial Title',
        'status' => 'draft',
        'is_locked' => false,
        'created_by' => $pm->id,
        'total_amount' => 0,
    ]);

    // 1. Update BOQ Version
    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->put(route('projects.boqs.update', [$project, $boq]), [
            'title' => 'Updated Title Milestone',
            'remarks' => 'Updated remarks',
        ]);

    $response->assertRedirect();
    $boq->refresh();
    expect($boq->title)->toBe('Updated Title Milestone');
    expect($boq->remarks)->toBe('Updated remarks');

    // 2. Create and update line item
    $item = BoqItem::create([
        'boq_version_id' => $boq->id,
        'project_id' => $project->id,
        'item_code' => 'CIV-001',
        'description' => 'Original Description',
        'unit' => 'cft',
        'planned_quantity' => 10,
        'unit_rate' => 50,
    ]);

    $boq->refresh();
    expect((float) $boq->total_amount)->toBe(500.00);

    $updateItemRes = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->put(route('projects.boqs.items.update', [$project, $boq, $item]), [
            'item_code' => 'CIV-001-MOD',
            'description' => 'Updated Description',
            'unit' => 'cum',
            'planned_quantity' => 20,
            'unit_rate' => 100,
        ]);

    $updateItemRes->assertRedirect();
    $item->refresh();
    expect($item->item_code)->toBe('CIV-001-MOD');
    expect($item->unit)->toBe('cum');
    expect((float) $item->planned_quantity)->toBe(20.0000);
    expect((float) $item->unit_rate)->toBe(100.00);
    expect((float) $item->total_amount)->toBe(2000.00);

    $boq->refresh();
    expect((float) $boq->total_amount)->toBe(2000.00);

    // 3. Lock it
    $boq->update(['is_locked' => true, 'status' => 'approved']);

    // 4. Unlock it
    $unlockRes = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('projects.boqs.unlock', [$project, $boq]));

    $unlockRes->assertRedirect();
    $boq->refresh();
    expect($boq->is_locked)->toBeFalse();
    expect($boq->status)->toBe('draft');
});



