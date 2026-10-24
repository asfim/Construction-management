<?php

use App\Models\ClientBill;
use App\Models\Company;
use App\Models\DailyProgressReport;
use App\Models\GoodsReceipt;
use App\Models\MaterialRequisition;
use App\Models\ProjectExpense;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('authorized company manager can view company edit page and update name, logo, address, and contact info', function () {
    Storage::fake('public');

    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $company = Company::where('code', 'HTP-CON')->first();

    // 1. Can view edit page
    $response = $this->actingAs($admin)->get(route('companies.edit', $company));
    $response->assertOk();
    $response->assertSee('Edit Company Settings');
    $response->assertSee($company->name);

    // 2. Can update company branding, logo, and address
    $fakeLogo = UploadedFile::fake()->image('skyline_logo.png', 400, 120);

    $updateResponse = $this->actingAs($admin)->put(route('companies.update', $company), [
        'code' => $company->code,
        'name' => 'Skyline Heights Construction Ltd.',
        'legal_name' => 'Skyline Heights Construction Bangladesh Limited',
        'trade_license' => 'TRAD/DNCC/998877/2026',
        'tin' => '112233445566',
        'bin' => '998877665-0101',
        'email' => 'contact@skyline.com.bd',
        'phone' => '+8801819998877',
        'address' => 'Level 12, Gulshan Center Point, Gulshan-2',
        'city' => 'Dhaka',
        'division' => 'Dhaka',
        'country' => 'Bangladesh',
        'currency' => 'BDT',
        'currency_symbol' => '৳',
        'fiscal_year_start' => '07-01',
        'is_active' => true,
        'logo' => $fakeLogo,
    ]);

    $updateResponse->assertRedirect(route('companies.show', $company));

    $freshCompany = $company->fresh();
    expect($freshCompany->name)->toBe('Skyline Heights Construction Ltd.');
    expect($freshCompany->phone)->toBe('+8801819998877');
    expect($freshCompany->bin)->toBe('998877665-0101');
    expect($freshCompany->address)->toBe('Level 12, Gulshan Center Point, Gulshan-2');
    expect($freshCompany->logo)->not->toBeNull();

    Storage::disk('public')->assertExists($freshCompany->logo);
});

test('print views dynamically render updated company name, address, phone, tax ids and logo bindings', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $company = Company::where('code', 'HTP-CON')->first();

    // Set distinctive branding on the company
    $company->update([
        'name' => 'Apex Infra Global Limited',
        'legal_name' => 'Apex Infra Global Construction Bangladesh Ltd.',
        'address' => 'Plot 88, Road 27, Block A, Banani, Dhaka-1213',
        'phone' => '+8801999888777',
        'email' => 'corporate@apexinfra.com.bd',
        'bin' => '123456789-0101',
        'tin' => '987654321012',
        'trade_license' => 'TRAD/DSCC/098765/2026',
    ]);

    // 1. Purchase Order Print View
    $po = PurchaseOrder::first();
    if ($po) {
        $poResponse = $this->actingAs($admin)->get(route('purchase-orders.show', $po));
        $poResponse->assertOk();
        $poResponse->assertSee('Apex Infra Global Limited');
        $poResponse->assertSee('Plot 88, Road 27, Block A, Banani');
        $poResponse->assertSee('+8801999888777');
        $poResponse->assertSee('123456789-0101');
        $poResponse->assertSee('print-document-header');
        $poResponse->assertSee('print-document-footer');
    }

    // 2. Goods Receipt Note (GRN) Print View
    $grn = GoodsReceipt::first();
    if ($grn) {
        $grnResponse = $this->actingAs($admin)->get(route('goods-receipts.show', $grn));
        $grnResponse->assertOk();
        $grnResponse->assertSee('Apex Infra Global Limited');
        $grnResponse->assertSee('Plot 88, Road 27, Block A, Banani');
        $grnResponse->assertSee('+8801999888777');
        $grnResponse->assertSee('print-document-header');
        $grnResponse->assertSee('print-document-footer');
    }

    // 3. Client Bill (RA Bill) Print View
    $bill = ClientBill::first();
    if ($bill) {
        $billResponse = $this->actingAs($admin)->get(route('client-bills.show', $bill));
        $billResponse->assertOk();
        $billResponse->assertSee('Apex Infra Global Limited');
        $billResponse->assertSee('+8801999888777');
        $billResponse->assertSee('print-document-header');
        $billResponse->assertSee('print-document-footer');
    }

    // 4. Project Expense Voucher Print View
    $expense = ProjectExpense::first();
    if ($expense) {
        $expenseResponse = $this->actingAs($admin)->get(route('project-expenses.show', $expense));
        $expenseResponse->assertOk();
        $expenseResponse->assertSee('Apex Infra Global Limited');
        $expenseResponse->assertSee('+8801999888777');
        $expenseResponse->assertSee('print-document-header');
        $expenseResponse->assertSee('print-document-footer');
    }

    // 5. Daily Progress Report (DPR) Print View
    $dpr = DailyProgressReport::first();
    if ($dpr) {
        $dprResponse = $this->actingAs($admin)->get(route('dpr.show', $dpr));
        $dprResponse->assertOk();
        $dprResponse->assertSee('Apex Infra Global Limited');
        $dprResponse->assertSee('+8801999888777');
        $dprResponse->assertSee('print-document-header');
        $dprResponse->assertSee('print-document-footer');
    }

    // 6. Material Requisition Print View
    $mr = MaterialRequisition::first();
    if ($mr) {
        $mrResponse = $this->actingAs($admin)->get(route('material-requisitions.show', $mr));
        $mrResponse->assertOk();
        $mrResponse->assertSee('Apex Infra Global Limited');
        $mrResponse->assertSee('+8801999888777');
        $mrResponse->assertSee('print-document-header');
        $mrResponse->assertSee('print-document-footer');
    }
});

test('single company mode enforces one company and redirects index and create to edit page', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $company = Company::first();

    // 1. Index redirects directly to the single company's edit page
    $indexResponse = $this->actingAs($admin)->get(route('companies.index'));
    $indexResponse->assertRedirect(route('companies.edit', $company));

    // 2. Create redirects to edit page with informational flash
    $createResponse = $this->actingAs($admin)->get(route('companies.create'));
    $createResponse->assertRedirect(route('companies.edit', $company));
    $createResponse->assertSessionHas('info');

    // 3. Attempting to store an additional company is rejected and redirected
    $initialCount = Company::count();
    $storeResponse = $this->actingAs($admin)->post(route('companies.store'), [
        'name' => 'Second Company Ltd',
        'code' => 'COMP-TWO',
        'currency' => 'BDT',
        'currency_symbol' => '৳',
        'fiscal_year_start' => '07-01',
    ]);
    $storeResponse->assertRedirect(route('companies.edit', $company));
    $storeResponse->assertSessionHas('warning');
    expect(Company::count())->toBe($initialCount);
});

