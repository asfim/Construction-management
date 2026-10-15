<?php

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;

test('authorized project manager can create a new project with decimal precision', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $pm = User::where('email', 'pm@htech.com.bd')->first();

    $response = $this->actingAs($pm)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('projects.store'), [
            'code' => 'PRJ-TEST-999',
            'name' => 'Mohakhali Flyover Maintenance Work',
            'client_name' => 'Dhaka North City Corporation',
            'contract_value' => '45000000.50',
            'budget_amount' => '38000000.25',
            'retention_percentage' => '7.5',
            'advance_recovery_percentage' => '10.0',
            'start_date' => '2026-11-01',
            'end_date' => '2027-11-01',
            'status' => 'planning',
        ]);

    $response->assertRedirect();

    $project = Project::withoutGlobalScopes()->where('code', 'PRJ-TEST-999')->first();
    expect($project)->not->toBeNull();
    expect((string) $project->contract_value)->toBe('45000000.50');
    expect((string) $project->budget_amount)->toBe('38000000.25');

    // Verify AuditLog was recorded automatically
    $auditLog = AuditLog::where('auditable_type', Project::class)
        ->where('auditable_id', $project->id)
        ->where('action', 'created')
        ->first();

    expect($auditLog)->not->toBeNull();
    expect($auditLog->user_name)->toBe($pm->name);
});

test('user without permission cannot create project', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $auditor = User::where('email', 'auditor@htech.com.bd')->first();

    $response = $this->actingAs($auditor)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('projects.store'), [
            'code' => 'PRJ-FAIL-001',
            'name' => 'Unauthorized Project Attempt',
            'contract_value' => '1000000.00',
            'budget_amount' => '800000.00',
            'status' => 'planning',
        ]);

    $response->assertForbidden();
});

test('authorized user can add a branch and see it in projects create dropdown', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();

    // 1. Create a branch via POST /branches
    $branchResponse = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('branches.store'), [
            'code' => 'BRN-CTG-001',
            'name' => 'Chittagong Port Regional Office',
            'city' => 'Chittagong',
            'division' => 'Chittagong',
            'is_active' => true,
        ]);

    $branchResponse->assertRedirect(route('branches.index'));

    // 2. Open /projects/create and verify branch is present in dropdown
    $createResponse = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('projects.create'));

    $createResponse->assertOk();
    $createResponse->assertSee('Chittagong Port Regional Office');
});

test('authorized user can add a project manager and see them in projects create dropdown', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();

    // 1. Create a new user with role 'Project Manager'
    $userResponse = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->post(route('users.store'), [
            'name' => 'Engr. Mahbubul Alam',
            'email' => 'mahbubul.pm@htech.com.bd',
            'phone' => '+8801711223344',
            'designation' => 'Lead Civil Project Manager',
            'role' => 'Project Manager',
            'password' => 'secret123',
            'is_active' => true,
        ]);

    $userResponse->assertRedirect(route('users.index'));

    $newUser = User::where('email', 'mahbubul.pm@htech.com.bd')->first();
    expect($newUser)->not->toBeNull();
    expect($newUser->hasRole('Project Manager'))->toBeTrue();

    // 2. Open /projects/create and verify project manager is present in dropdown
    $createResponse = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->get(route('projects.create'));

    $createResponse->assertOk();
    $createResponse->assertSee('Engr. Mahbubul Alam');
    $createResponse->assertSee('Lead Civil Project Manager');
});

test('quick add ajax endpoints for branch and manager return json', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();

    // Branch AJAX
    $branchAjax = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->postJson(route('branches.store'), [
            'code' => 'BRN-SYL-001',
            'name' => 'Sylhet Tea Estate Office',
            'city' => 'Sylhet',
            'is_active' => true,
        ]);

    $branchAjax->assertOk();
    $branchAjax->assertJsonFragment([
        'success' => true,
        'name' => 'Sylhet Tea Estate Office',
    ]);

    // Project Manager AJAX
    $managerAjax = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->postJson(route('users.store'), [
            'name' => 'Engr. Farhana Yasmin',
            'email' => 'farhana.pm@htech.com.bd',
            'role' => 'Project Manager',
            'designation' => 'Senior Project Director',
            'is_active' => true,
        ]);

    $managerAjax->assertOk();
    $managerAjax->assertJsonFragment([
        'success' => true,
        'name' => 'Engr. Farhana Yasmin',
        'role' => 'Project Manager',
    ]);
});

test('authorized user can update project progress percent', function () {
    $company = Company::where('code', 'HTP-CON')->first();
    $superadmin = User::where('email', 'superadmin@htech.com.bd')->first();
    $project = Project::first();

    $response = $this->actingAs($superadmin)
        ->withSession(['active_company_id' => $company->id])
        ->patch(route('projects.progress.update', $project), [
            'progress_percent' => '45.5',
            'status' => 'active',
        ]);

    $response->assertRedirect();
    $project->refresh();
    expect((float) $project->progress_percent)->toBe(45.5);
    expect($project->status)->toBe('active');
});

