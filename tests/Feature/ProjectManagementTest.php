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
