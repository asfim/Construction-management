<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('super admin can view roles and permissions index', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();

    $response = $this->actingAs($admin)->get(route('roles.index'));

    $response->assertOk();
    $response->assertSee('Roles &amp; Permissions', false);
    $response->assertSee('Access Control Matrix');
    $response->assertSee('Super Admin');
    $response->assertSee('Project Manager');
    $response->assertSee('Site Engineer');
    $response->assertSee('Quantity Surveyor (QS)');
});

test('super admin can view role show and edit pages', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $pmRole = Role::findByName('Project Manager');

    // 1. Show page
    $showResponse = $this->actingAs($admin)->get(route('roles.show', $pmRole));
    $showResponse->assertOk();
    $showResponse->assertSee('Project Manager');
    $showResponse->assertSee('Granted Permissions By Module');

    // 2. Edit page
    $editResponse = $this->actingAs($admin)->get(route('roles.edit', $pmRole));
    $editResponse->assertOk();
    $editResponse->assertSee('Configure Role: Project Manager');
    $editResponse->assertSee('Module Permissions Matrix');
});

test('super admin can create a new custom role with selected permissions', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();

    $response = $this->actingAs($admin)->post(route('roles.store'), [
        'name' => 'Junior Site Supervisor',
        'description' => 'Supervises daily concrete pouring and labor attendance',
        'permissions' => [
            'dpr.view',
            'dpr.create',
            'item.view',
            'mr.view',
        ],
    ]);

    $response->assertRedirect(route('roles.index'));
    $response->assertSessionHas('success');

    $role = Role::findByName('Junior Site Supervisor');
    expect($role)->not->toBeNull();
    expect($role->hasPermissionTo('dpr.view'))->toBeTrue();
    expect($role->hasPermissionTo('dpr.create'))->toBeTrue();
    expect($role->hasPermissionTo('project.delete'))->toBeFalse();
});

test('super admin can update permissions for an existing role', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $role = Role::firstOrCreate(['name' => 'Custom Field Auditor', 'guard_name' => 'web']);
    $role->givePermissionTo('audit.view');

    $response = $this->actingAs($admin)->put(route('roles.update', $role), [
        'name' => 'Lead Field Auditor',
        'permissions' => [
            'audit.view',
            'report.view',
        ],
    ]);

    $response->assertRedirect(route('roles.index'));
    $response->assertSessionHas('success');

    $updatedRole = Role::findByName('Lead Field Auditor');
    expect($updatedRole->hasPermissionTo('report.view'))->toBeTrue();
});

test('system core roles cannot be deleted', function () {
    $admin = User::where('email', 'superadmin@htech.com.bd')->first();
    $pmRole = Role::findByName('Project Manager');

    $response = $this->actingAs($admin)->delete(route('roles.destroy', $pmRole));
    $response->assertRedirect(route('roles.index'));
    $response->assertSessionHas('error');
    expect(Role::where('name', 'Project Manager')->exists())->toBeTrue();
});

test('unauthenticated guest is redirected to login from roles page', function () {
    $response = $this->get(route('roles.index'));
    $response->assertRedirect(route('login'));
});
