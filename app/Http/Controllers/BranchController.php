<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('branch.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();

        $branches = Branch::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->withCount('projects')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('branches.index', compact('branches', 'search'));
    }

    public function create(): View
    {
        $this->authorize('branch.manage');

        return view('branches.create');
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('branch.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'division' => ['nullable', 'string', 'max:100'],
            'is_head_office' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_head_office'] = $request->boolean('is_head_office');
        $validated['is_active'] = $request->boolean('is_active', true);

        Branch::create($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Branch created successfully.');
    }

    public function edit(Branch $branch): View
    {
        $this->authorize('branch.manage');

        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $this->authorize('branch.manage');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'division' => ['nullable', 'string', 'max:100'],
            'is_head_office' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_head_office'] = $request->boolean('is_head_office');
        $validated['is_active'] = $request->boolean('is_active');

        $branch->update($validated);

        return redirect()->route('branches.index')
            ->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $this->authorize('branch.manage');

        if ($branch->projects()->exists()) {
            return back()->with('error', 'Cannot delete branch because active projects are assigned to it.');
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Branch deleted successfully.');
    }
}
