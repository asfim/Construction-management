<?php

use App\Models\Branch;
use App\Models\ItemCategory;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;

test('procurement manager can create and edit item category', function () {
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();

    $response = $this->actingAs($procurement)->post(route('item-categories.store'), [
        'code' => 'CAT-ELECTRICAL',
        'name' => 'Electrical & Substation Items',
        'name_bn' => 'বৈদ্যুতিক সরঞ্জাম',
        'description' => 'Wires, cables, switch gears and transformers',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('item-categories.index'));
    $this->assertDatabaseHas('item_categories', [
        'code' => 'CAT-ELECTRICAL',
        'name' => 'Electrical & Substation Items',
    ]);

    $category = ItemCategory::where('code', 'CAT-ELECTRICAL')->first();

    $editResponse = $this->actingAs($procurement)->put(route('item-categories.update', $category), [
        'code' => 'CAT-ELECTRICAL',
        'name' => 'Electrical & Substation Equipments',
        'name_bn' => 'বৈদ্যুতিক সরঞ্জাম ও সাবস্টেশন',
        'is_active' => true,
    ]);

    $editResponse->assertRedirect(route('item-categories.index'));
    expect($category->fresh()->name)->toBe('Electrical & Substation Equipments');
});

test('procurement manager can create parent category and subcategory linked to parent', function () {
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();

    // 1. Create a Parent Category
    $parentRes = $this->actingAs($procurement)->post(route('item-categories.store'), [
        'code' => 'CAT-HVAC-MAIN',
        'name' => 'HVAC & Mechanical',
        'category_type' => 'parent',
        'is_active' => true,
    ]);
    $parentRes->assertRedirect(route('item-categories.index'));

    $parentCat = ItemCategory::where('code', 'CAT-HVAC-MAIN')->first();
    expect($parentCat)->not->toBeNull();
    expect($parentCat->parent_id)->toBeNull();

    // 2. Create a Subcategory linked to the Parent
    $subRes = $this->actingAs($procurement)->post(route('item-categories.store'), [
        'code' => 'CAT-HVAC-DUCT',
        'name' => 'GI Air Ducts & Dampers',
        'category_type' => 'sub',
        'parent_id' => $parentCat->id,
        'is_active' => true,
    ]);
    $subRes->assertRedirect(route('item-categories.index'));

    $subCat = ItemCategory::where('code', 'CAT-HVAC-DUCT')->first();
    expect($subCat)->not->toBeNull();
    expect($subCat->parent_id)->toBe($parentCat->id);
    expect($parentCat->children()->count())->toBe(1);

    // 3. Test filter tabs on index
    $indexParent = $this->actingAs($procurement)->get(route('item-categories.index', ['type' => 'parent']));
    $indexParent->assertOk();
    $indexParent->assertSee('CAT-HVAC-MAIN');
    $indexParent->assertDontSee('CAT-HVAC-DUCT');

    $indexSub = $this->actingAs($procurement)->get(route('item-categories.index', ['type' => 'sub']));
    $indexSub->assertOk();
    $indexSub->assertSee('CAT-HVAC-DUCT');
    $indexSub->assertSee('Subcategory');
});

test('authorized manager can create and edit unit of measurement', function () {
    $procurement = User::where('email', 'procurement@htech.com.bd')->first();

    $response = $this->actingAs($procurement)->post(route('units.store'), [
        'name' => 'Cubic Meter',
        'name_bn' => 'ঘন মিটার',
        'symbol' => 'm3',
        'type' => 'volume',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('units.index'));
    $this->assertDatabaseHas('units', [
        'symbol' => 'm3',
        'type' => 'volume',
    ]);
});

test('storekeeper can create a new site store warehouse', function () {
    $storekeeper = User::where('email', 'storekeeper@htech.com.bd')->first();
    $project = Project::first();

    $response = $this->actingAs($storekeeper)->post(route('warehouses.store'), [
        'code' => 'WH-TEST-SITE-01',
        'name' => 'Dhanmondi Project Site Store',
        'location' => 'Road 27, Dhanmondi, Dhaka',
        'project_id' => $project->id,
        'storekeeper_id' => $storekeeper->id,
        'is_central' => false,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('warehouses.index'));
    $this->assertDatabaseHas('warehouses', [
        'code' => 'WH-TEST-SITE-01',
        'name' => 'Dhanmondi Project Site Store',
        'is_central' => 0,
    ]);
});

test('managing director can create a new company branch', function () {
    $md = User::where('email', 'owner@htech.com.bd')->first();

    $response = $this->actingAs($md)->post(route('branches.store'), [
        'code' => 'BR-CTG-01',
        'name' => 'Chattogram Regional Branch',
        'phone' => '+8801811223344',
        'email' => 'ctg.branch@htech.com.bd',
        'address' => 'Agrabad C/A, Chattogram',
        'city' => 'Chattogram',
        'division' => 'Chattogram',
        'is_head_office' => false,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('branches.index'));
    $this->assertDatabaseHas('branches', [
        'code' => 'BR-CTG-01',
        'city' => 'Chattogram',
    ]);
});
