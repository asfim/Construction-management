<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('user.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();
        $roleFilter = $request->string('role')->trim();

        $users = User::query()
            ->when($companyId && ! auth()->user()->hasRole('Super Admin'), function ($q) use ($companyId) {
                $q->whereHas('companies', fn ($sub) => $sub->where('companies.id', $companyId));
            })
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter->isNotEmpty(), function ($q) use ($roleFilter) {
                $q->whereHas('roles', fn ($sub) => $sub->where('name', $roleFilter));
            })
            ->with(['roles', 'companies'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::whereNotIn('name', ['Super Admin'])->orderBy('name')->get();
        $totalManagers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Project Manager', 'Project Director']))->count();

        return view('users.index', compact('users', 'roles', 'search', 'roleFilter', 'totalManagers'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('user.manage');

        $companyId = $tenantManager->currentCompanyId();
        $branches = Branch::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('is_active', true)
            ->get();

        $roles = Role::whereNotIn('name', ['Super Admin'])->orderBy('name')->get();
        $defaultRole = $request->query('role', 'Project Manager');

        return view('users.create', compact('branches', 'roles', 'defaultRole'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse|JsonResponse
    {
        $this->authorize('user.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'exists:roles,name'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? $validated['role'],
            'password' => Hash::make($validated['password'] ?? 'password123'),
            'current_company_id' => $companyId,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Assign Role
        $user->assignRole($validated['role']);

        // Attach to company pivot
        if ($companyId) {
            $user->companies()->syncWithoutDetaching([
                $companyId => [
                    'branch_id' => $validated['branch_id'] ?? null,
                    'designation' => $validated['designation'] ?? $validated['role'],
                    'is_default' => true,
                ],
            ]);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$validated['role']} created successfully.",
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'designation' => $user->designation ?? $validated['role'],
                    'role' => $validated['role'],
                ],
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} created successfully with role '{$validated['role']}'.");
    }

    public function edit(User $user, TenantManager $tenantManager): View
    {
        $this->authorize('user.manage');

        $companyId = $tenantManager->currentCompanyId();
        $branches = Branch::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->where('is_active', true)
            ->get();

        $roles = Role::whereNotIn('name', ['Super Admin'])->orderBy('name')->get();
        $userRole = $user->roles->first()?->name ?? 'Project Manager';
        $userBranchId = $user->companies()->where('companies.id', $companyId)->first()?->pivot?->branch_id;

        return view('users.edit', compact('user', 'branches', 'roles', 'userRole', 'userBranchId'));
    }

    public function update(Request $request, User $user, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('user.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'designation' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'exists:roles,name'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'password' => ['nullable', 'string', 'min:6'],
            'is_active' => ['boolean'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        if ($companyId) {
            $user->companies()->syncWithoutDetaching([
                $companyId => [
                    'branch_id' => $validated['branch_id'] ?? null,
                    'designation' => $validated['designation'] ?? $validated['role'],
                ],
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', "User {$user->name} updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('user.manage');

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deactivated/deleted successfully.');
    }
}
