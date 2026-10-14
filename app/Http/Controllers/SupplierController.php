<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('supplier.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();

        $suppliers = Supplier::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('bin', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create(): View
    {
        $this->authorize('supplier.manage');

        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $validated['company_id'] = $tenantManager->currentCompanyId();

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('erp.messages.created_successfully'));
    }

    public function show(Supplier $supplier): View
    {
        $this->authorize('supplier.view');

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        $this->authorize('supplier.manage');

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', __('erp.messages.updated_successfully'));
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('supplier.manage');

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', __('erp.messages.deleted_successfully'));
    }
}
