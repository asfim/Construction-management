<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Protected core roles that cannot be deleted or have their name changed.
     */
    public const SYSTEM_ROLES = [
        'Super Admin',
        'Company Owner',
        'Project Manager',
        'Site Engineer',
        'Quantity Surveyor (QS)',
        'Procurement Manager',
        'Storekeeper',
        'Accounts Manager',
    ];

    public function index(Request $request): View
    {
        $this->authorize('role.view');

        $search = $request->string('search')->trim()->value();

        $roles = Role::query()
            ->when(! empty($search), fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount(['permissions', 'users'])
            ->orderByRaw("CASE WHEN name = 'Super Admin' THEN 0 WHEN name = 'Company Owner' THEN 1 ELSE 2 END")
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $totalAssignedUsers = \App\Models\User::has('roles')->count();
        $systemRoles = self::SYSTEM_ROLES;

        return view('roles.index', compact(
            'roles',
            'search',
            'totalRoles',
            'totalPermissions',
            'totalAssignedUsers',
            'systemRoles'
        ));
    }

    public function create(): View
    {
        $this->authorize('role.manage');

        $permissionGroups = self::permissionGroups();
        $allPermissions = Permission::orderBy('name')->get();

        return view('roles.create', compact('permissionGroups', 'allPermissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('role.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Record Audit Log
        AuditLog::create([
            'company_id' => auth()->user()->currentCompanyId ?? null,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'created',
            'auditable_type' => Role::class,
            'auditable_id' => $role->id,
            'description' => "Created role '{$role->name}' with " . count($validated['permissions'] ?? []) . ' permissions',
            'new_values' => [
                'name' => $role->name,
                'permissions_count' => count($validated['permissions'] ?? []),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Role '{$role->name}' has been created successfully with assigned permissions.");
    }

    public function show(Role $role): View
    {
        $this->authorize('role.view');

        $role->loadCount(['permissions', 'users']);
        $role->load('users');
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissionGroups = self::permissionGroups();
        $systemRoles = self::SYSTEM_ROLES;

        return view('roles.show', compact('role', 'rolePermissions', 'permissionGroups', 'systemRoles'));
    }

    public function edit(Role $role): View
    {
        $this->authorize('role.manage');

        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissionGroups = self::permissionGroups();
        $systemRoles = self::SYSTEM_ROLES;

        return view('roles.edit', compact('role', 'rolePermissions', 'permissionGroups', 'systemRoles'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('role.manage');

        $isSystemRole = in_array($role->name, self::SYSTEM_ROLES, true);

        $rules = [
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ];

        // System role names cannot be renamed to protect application stability
        if (! $isSystemRole) {
            $rules['name'] = ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)];
        }

        $validated = $request->validate($rules);

        $oldPermissions = $role->permissions->pluck('name')->toArray();

        if (! $isSystemRole && isset($validated['name'])) {
            $role->update(['name' => $validated['name']]);
        }

        // Super Admin always maintains all permissions
        if ($role->name === 'Super Admin') {
            $role->syncPermissions(Permission::all());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        // Record Audit Log
        AuditLog::create([
            'company_id' => auth()->user()->currentCompanyId ?? null,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'updated',
            'auditable_type' => Role::class,
            'auditable_id' => $role->id,
            'description' => "Updated permissions for role '{$role->name}'",
            'old_values' => ['permissions_count' => count($oldPermissions)],
            'new_values' => ['permissions_count' => count($validated['permissions'] ?? [])],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', "Permissions for role '{$role->name}' have been updated successfully.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role.manage');

        if (in_array($role->name, self::SYSTEM_ROLES, true)) {
            return redirect()->route('roles.index')
                ->with('error', "Core system role '{$role->name}' cannot be deleted as it is required by the ERP system.");
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', "Cannot delete role '{$role->name}' because {$role->users()->count()} user(s) are currently assigned to it.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role '{$roleName}' has been removed successfully.");
    }

    /**
     * Human-categorized construction ERP permission groups.
     */
    public static function permissionGroups(): array
    {
        return [
            'Company & Organization' => [
                'icon' => 'bi-buildings',
                'description' => 'Company profile, branches and organizational parameters',
                'permissions' => [
                    'company.view' => 'View Company Settings & Profile',
                    'company.manage' => 'Manage & Update Company Details',
                    'branch.view' => 'View Company Branches',
                    'branch.manage' => 'Create & Edit Branches',
                    'settings.manage' => 'Manage System Configuration',
                ],
            ],
            'User & Access Control' => [
                'icon' => 'bi-shield-lock',
                'description' => 'Staff accounts, role definitions and permission matrix',
                'permissions' => [
                    'user.view' => 'View Staff & Users List',
                    'user.manage' => 'Create, Edit & Deactivate Users',
                    'role.view' => 'View Roles & Permission Matrix',
                    'role.manage' => 'Create & Modify Roles & Permissions',
                ],
            ],
            'Project Management' => [
                'icon' => 'bi-kanban',
                'description' => 'Projects master, progress updates and manager assignments',
                'permissions' => [
                    'project.view' => 'View Projects & Status',
                    'project.create' => 'Create New Projects',
                    'project.edit' => 'Edit Project Details & Timeline',
                    'project.delete' => 'Delete / Archive Projects',
                    'project.assign' => 'Assign Managers & Staff to Projects',
                ],
            ],
            'BOQ & Rate Analysis' => [
                'icon' => 'bi-table',
                'description' => 'Bills of Quantities, item line estimation and unit rate analysis',
                'permissions' => [
                    'boq.view' => 'View Project BOQs',
                    'boq.manage' => 'Add, Edit & Unlock BOQ Line Items',
                    'boq.approve' => 'Lock & Approve Official BOQ Baseline',
                    'boq.import' => 'Import BOQ from Excel/Spreadsheets',
                ],
            ],
            'Budget & Cost Codes' => [
                'icon' => 'bi-calculator',
                'description' => 'Project budgets, WBS cost codes and variance tracking',
                'permissions' => [
                    'budget.view' => 'View Project Budget & Variances',
                    'budget.manage' => 'Create & Modify Budget Lines',
                    'budget.approve' => 'Approve Official Budget Baseline',
                    'cost-code.view' => 'View Cost Codes & WBS',
                    'cost-code.manage' => 'Create & Manage Cost Codes',
                ],
            ],
            'Procurement & Purchase' => [
                'icon' => 'bi-cart-check',
                'description' => 'Material requisitions, RFQs, purchase orders and suppliers',
                'permissions' => [
                    'supplier.view' => 'View Suppliers Directory',
                    'supplier.manage' => 'Create & Edit Suppliers & Terms',
                    'mr.view' => 'View Material Requisitions (MR)',
                    'mr.create' => 'Create & Submit Material Requisitions',
                    'mr.approve' => 'Approve Material Requisitions',
                    'rfq.view' => 'View Quotation Requests (RFQ)',
                    'rfq.manage' => 'Manage Comparative Statements & RFQs',
                    'po.view' => 'View Purchase Orders (PO)',
                    'po.create' => 'Create Purchase Orders with VAT/Taxes',
                    'po.approve' => 'Approve & Release Purchase Orders',
                ],
            ],
            'Inventory & Warehouses' => [
                'icon' => 'bi-boxes',
                'description' => 'Material catalog, site warehouses, GRN and stock issues',
                'permissions' => [
                    'item.view' => 'View Material Catalog & Categories',
                    'item.manage' => 'Create, Edit & Delete Material Items',
                    'warehouse.view' => 'View Site Warehouses / Stores',
                    'warehouse.manage' => 'Create & Edit Site Warehouses',
                    'grn.view' => 'View Goods Receipt Notes (GRN)',
                    'grn.create' => 'Receive Materials & Create GRN',
                    'grn.approve' => 'Approve & Post GRN to Stock',
                    'stock.view' => 'View Realtime Stock Inventory Ledger',
                    'stock.issue' => 'Issue Materials to Site Execution',
                    'stock.adjust' => 'Perform Stock Adjustments & Reconciliation',
                ],
            ],
            'Site Execution & DPR' => [
                'icon' => 'bi-clipboard-data',
                'description' => 'Daily progress reports, site manpower and plant tracking',
                'permissions' => [
                    'dpr.view' => 'View Daily Progress Reports (DPR)',
                    'dpr.create' => 'Submit Daily Progress Reports',
                    'dpr.approve' => 'Approve Official Site DPRs',
                ],
            ],
            'Measurement & Client Billing' => [
                'icon' => 'bi-receipt-cutoff',
                'description' => 'Measurement book (MB) and running account (RA) client bills',
                'permissions' => [
                    'mb.view' => 'View Measurement Book (MB)',
                    'mb.create' => 'Record Site Work Measurements',
                    'mb.approve' => 'Approve Measurement Sheets',
                    'bill.view' => 'View Client Running Account (RA) Bills',
                    'bill.create' => 'Generate Client RA Bills with Deductions',
                    'bill.certify' => 'Certify Client Bills & Record Payments',
                ],
            ],
            'Finance & Accounts' => [
                'icon' => 'bi-cash-coin',
                'description' => 'Project expenses, site petty cash and journal vouchers',
                'permissions' => [
                    'account.view' => 'View Accounts & Project Cashbook',
                    'account.manage' => 'Manage Expense Heads & Accounts',
                    'journal.view' => 'View Journal Entries & Vouchers',
                    'journal.create' => 'Record Expense Vouchers',
                    'journal.post' => 'Post & Settle Account Vouchers',
                    'tax.view' => 'View Tax & VAT Schedules',
                    'tax.manage' => 'Configure VAT & Withholding Tax Rates',
                ],
            ],
            'Audit & Security' => [
                'icon' => 'bi-shield-check',
                'description' => 'System activity trail, security audits and compliance reports',
                'permissions' => [
                    'audit.view' => 'View Audit Logs & Security History',
                    'report.view' => 'Generate & View Executive ERP Reports',
                    'report.export' => 'Export Reports to Excel/PDF/CSV',
                ],
            ],
        ];
    }
}
