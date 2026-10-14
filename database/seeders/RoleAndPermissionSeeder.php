<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // Company & Branch
            'company.view', 'company.manage',
            'branch.view', 'branch.manage',
            'user.view', 'user.manage',
            'role.view', 'role.manage',
            'settings.manage',

            // Project Master
            'project.view', 'project.create', 'project.edit', 'project.delete', 'project.assign',

            // BOQ & Estimation
            'boq.view', 'boq.manage', 'boq.approve', 'boq.import',

            // Budget & Cost Codes
            'budget.view', 'budget.manage', 'budget.approve',
            'cost-code.view', 'cost-code.manage',

            // Procurement
            'supplier.view', 'supplier.manage',
            'mr.view', 'mr.create', 'mr.approve',
            'rfq.view', 'rfq.manage',
            'po.view', 'po.create', 'po.approve',

            // Inventory & Materials
            'item.view', 'item.manage',
            'warehouse.view', 'warehouse.manage',
            'grn.view', 'grn.create', 'grn.approve',
            'stock.view', 'stock.issue', 'stock.adjust',

            // Site Execution & DPR
            'dpr.view', 'dpr.create', 'dpr.approve',

            // Measurement & Billing
            'mb.view', 'mb.create', 'mb.approve',
            'bill.view', 'bill.create', 'bill.certify',

            // Finance & Accounts
            'account.view', 'account.manage',
            'journal.view', 'journal.create', 'journal.post',
            'tax.view', 'tax.manage',

            // Audit & Reports
            'audit.view',
            'report.view', 'report.export',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Define the 24 roles from Bangladesh Construction Management ERP specification
        $roles = [
            'Super Admin',
            'Company Owner',
            'Managing Director',
            'Project Director',
            'Project Manager',
            'Site Engineer',
            'Planning Engineer',
            'Quantity Surveyor (QS)',
            'Estimator',
            'Procurement Manager',
            'Storekeeper',
            'Accounts Manager',
            'Accountant',
            'HR Manager',
            'Safety Officer',
            'Quality Inspector',
            'Equipment Manager',
            'Sales Manager',
            'CRM Executive',
            'Contractor/Subcontractor',
            'Supplier Portal User',
            'Client Portal User',
            'Auditor',
            'Viewer',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // Super Admin gets all permissions
        $superAdminRole = Role::findByName('Super Admin', 'web');
        $superAdminRole->givePermissionTo(Permission::all());

        // Company Owner gets all permissions
        $ownerRole = Role::findByName('Company Owner', 'web');
        $ownerRole->givePermissionTo(Permission::all());

        // Managing Director gets executive permissions
        $mdRole = Role::findByName('Managing Director', 'web');
        $mdRole->givePermissionTo([
            'company.view', 'branch.view', 'branch.manage', 'project.view', 'project.create', 'project.edit', 'project.assign',
            'boq.view', 'boq.approve', 'budget.view', 'budget.approve', 'mr.view', 'mr.approve',
            'po.view', 'po.approve', 'grn.view', 'warehouse.view', 'warehouse.manage', 'stock.view', 'dpr.view', 'mb.view', 'bill.view',
            'bill.certify', 'account.view', 'journal.view', 'tax.view', 'audit.view', 'report.view', 'report.export',
        ]);

        // Project Manager
        $pmRole = Role::findByName('Project Manager', 'web');
        $pmRole->givePermissionTo([
            'project.view', 'project.create', 'project.edit', 'project.assign', 'branch.view',
            'boq.view', 'boq.manage', 'boq.approve', 'budget.view', 'budget.manage', 'cost-code.view',
            'item.view', 'item.manage',
            'mr.view', 'mr.create', 'mr.approve', 'po.view', 'grn.view', 'grn.approve',
            'warehouse.view', 'warehouse.manage', 'stock.view', 'stock.issue',
            'dpr.view', 'dpr.create', 'dpr.approve', 'mb.view', 'mb.create', 'mb.approve',
            'bill.view', 'bill.create', 'bill.certify', 'report.view', 'report.export',
        ]);

        // Site Engineer
        $seRole = Role::findByName('Site Engineer', 'web');
        $seRole->givePermissionTo([
            'project.view', 'boq.view', 'mr.view', 'mr.create', 'grn.view', 'warehouse.view', 'stock.view',
            'dpr.view', 'dpr.create', 'mb.view', 'mb.create', 'report.view',
        ]);

        // Quantity Surveyor (QS)
        $qsRole = Role::findByName('Quantity Surveyor (QS)', 'web');
        $qsRole->givePermissionTo([
            'project.view', 'boq.view', 'boq.manage', 'boq.import', 'budget.view', 'cost-code.view',
            'mb.view', 'mb.create', 'mb.approve', 'bill.view', 'bill.create', 'report.view', 'report.export',
        ]);

        // Procurement Manager
        $procurementRole = Role::findByName('Procurement Manager', 'web');
        $procurementRole->givePermissionTo([
            'supplier.view', 'supplier.manage', 'item.view', 'item.manage', 'mr.view',
            'rfq.view', 'rfq.manage', 'po.view', 'po.create', 'po.approve', 'grn.view', 'grn.approve',
            'warehouse.view', 'warehouse.manage', 'stock.view', 'report.view', 'report.export',
        ]);

        // Storekeeper
        $storekeeperRole = Role::findByName('Storekeeper', 'web');
        $storekeeperRole->givePermissionTo([
            'item.view', 'warehouse.view', 'warehouse.manage', 'grn.view', 'grn.create', 'grn.approve',
            'stock.view', 'stock.issue', 'stock.adjust', 'mr.view', 'po.view',
        ]);

        // Accounts Manager & Accountant
        $accountsRole = Role::findByName('Accounts Manager', 'web');
        $accountsRole->givePermissionTo([
            'account.view', 'account.manage', 'journal.view', 'journal.create', 'journal.post',
            'tax.view', 'tax.manage', 'po.view', 'grn.view', 'bill.view', 'bill.certify',
            'audit.view', 'report.view', 'report.export',
        ]);

        $accountantRole = Role::findByName('Accountant', 'web');
        $accountantRole->givePermissionTo([
            'account.view', 'journal.view', 'journal.create', 'tax.view',
            'po.view', 'grn.view', 'bill.view', 'report.view',
        ]);

        // Auditor
        $auditorRole = Role::findByName('Auditor', 'web');
        $auditorRole->givePermissionTo([
            'company.view', 'branch.view', 'project.view', 'boq.view', 'budget.view',
            'po.view', 'grn.view', 'stock.view', 'mb.view', 'bill.view', 'account.view',
            'journal.view', 'tax.view', 'audit.view', 'report.view',
        ]);

        // Viewer
        $viewerRole = Role::findByName('Viewer', 'web');
        $viewerRole->givePermissionTo([
            'project.view', 'dpr.view', 'report.view',
        ]);
    }
}
