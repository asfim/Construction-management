<?php

use App\Models\AuditLog;
use App\Models\User;

test('authorized user can view audit logs page with kpi cards and activity table', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();

    $response = $this->actingAs($admin)->get(route('audit-logs.index'));

    $response->assertOk();
    $response->assertSee('Audit Trail & Security Logs');
    $response->assertSee('Total Logs');
    $response->assertSee('Created');
    $response->assertSee('Updated');
    $response->assertSee('Deleted');
    $response->assertSee('Export CSV');
});

test('user can filter audit logs by action and search term', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();

    // 1. Filter by created
    $createdResponse = $this->actingAs($admin)->get(route('audit-logs.index', ['action' => 'created']));
    $createdResponse->assertOk();
    $createdResponse->assertSee('Active Filters:');
    $createdResponse->assertSee('Action: Created');

    // 2. Filter by search term
    $searchResponse = $this->actingAs($admin)->get(route('audit-logs.index', ['search' => 'Company']));
    $searchResponse->assertOk();
    $searchResponse->assertSee('Active Filters:');
    $searchResponse->assertSee('Company');
});

test('authorized user can export audit logs to csv', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();

    $response = $this->actingAs($admin)->get(route('audit-logs.index', ['export' => 'csv']));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
});

test('unauthenticated guest is redirected to login', function () {
    $response = $this->get(route('audit-logs.index'));
    $response->assertRedirect(route('login'));
});
