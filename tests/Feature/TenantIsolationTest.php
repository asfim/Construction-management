<?php

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

test('user from company A cannot view company B projects', function () {
    // Company A
    $companyA = Company::where('code', 'HTP-CON')->first();

    // Create Company B
    $companyB = Company::create([
        'code' => 'COMP-B',
        'name' => 'Competitor Builders Ltd.',
        'currency' => 'BDT',
        'currency_symbol' => '৳',
        'is_active' => true,
    ]);

    // Create Project in Company B
    $projectB = Project::withoutGlobalScopes()->create([
        'company_id' => $companyB->id,
        'code' => 'PRJ-B-001',
        'name' => 'Secret Commercial Plaza',
        'contract_value' => 50000000.00,
        'budget_amount' => 45000000.00,
        'status' => 'active',
    ]);

    // User only belonging to Company A
    $userA = User::where('email', 'pm@htech.com.bd')->first();

    // Acting as User A, attempt to view Company B's project
    $response = $this->actingAs($userA)
        ->withSession(['active_company_id' => $companyA->id])
        ->get(route('projects.show', $projectB));

    // Global scope must prevent User A from finding Company B project (404)
    $response->assertNotFound();
});

test('non-admin user cannot switch to a company they do not belong to', function () {
    $companyB = Company::firstOrCreate(
        ['code' => 'COMP-B'],
        ['name' => 'Competitor Builders Ltd.', 'currency' => 'BDT', 'currency_symbol' => '৳', 'is_active' => true]
    );
    $userA = User::where('email', 'site.engineer@htech.com.bd')->first();

    $response = $this->actingAs($userA)
        ->post(route('company.switch'), [
            'company_id' => $companyB->id,
        ]);

    $response->assertSessionHas('error');
    expect(session('active_company_id'))->not->toBe($companyB->id);
});
