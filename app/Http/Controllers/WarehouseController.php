<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('warehouse.view');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();
        $search = $request->string('search')->trim();

        $warehouses = Warehouse::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where(fn ($sub) => $sub->where('project_id', $projectId)->orWhere('is_central', true)))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->with(['project', 'storekeeper'])
            ->withCount('transactions')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('warehouses.index', compact('warehouses', 'search'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('warehouse.manage');

        $companyId = $tenantManager->currentCompanyId();
        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $storekeepers = User::whereHas('roles', fn ($q) => $q->where('name', 'Storekeeper'))
            ->orWhereHas('companies', fn ($q) => $q->where('companies.id', $companyId))
            ->get();

        return view('warehouses.create', compact('projects', 'storekeepers'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('warehouse.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'storekeeper_id' => ['nullable', 'exists:users,id'],
            'is_central' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_central'] = $request->boolean('is_central');
        $validated['is_active'] = $request->boolean('is_active', true);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'গোডাউন / সাইট স্টোর সফলভাবে যোগ করা হয়েছে।');
    }

    public function edit(Warehouse $warehouse, TenantManager $tenantManager): View
    {
        $this->authorize('warehouse.manage');

        $companyId = $tenantManager->currentCompanyId();
        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $storekeepers = User::whereHas('roles', fn ($q) => $q->where('name', 'Storekeeper'))
            ->orWhereHas('companies', fn ($q) => $q->where('companies.id', $companyId))
            ->get();

        return view('warehouses.edit', compact('warehouse', 'projects', 'storekeepers'));
    }

    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $this->authorize('warehouse.manage');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'storekeeper_id' => ['nullable', 'exists:users,id'],
            'is_central' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_central'] = $request->boolean('is_central');
        $validated['is_active'] = $request->boolean('is_active');

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')
            ->with('success', 'গোডাউনের তথ্য সফলভাবে হালনাগাদ করা হয়েছে।');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        $this->authorize('warehouse.manage');

        if ($warehouse->transactions()->exists()) {
            return back()->with('error', 'এই গোডাউনে স্টক ট্রানজ্যাকশন রেকর্ড রয়েছে, তাই মুছে ফেলা সম্ভব নয়।');
        }

        $warehouse->delete();

        return redirect()->route('warehouses.index')
            ->with('success', 'গোডাউন সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
