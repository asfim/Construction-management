<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemCategoryController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('item.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();

        $categories = ItemCategory::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->with(['parent', 'items'])
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('item-categories.index', compact('categories', 'search'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();
        $parentCategories = ItemCategory::where('company_id', $companyId)->where('is_active', true)->get();

        return view('item-categories.create', compact('parentCategories'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:item_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = $request->boolean('is_active', true);

        ItemCategory::create($validated);

        return redirect()->route('item-categories.index')
            ->with('success', 'আইটেম ক্যাটাগরি সফলভাবে তৈরি হয়েছে।');
    }

    public function edit(ItemCategory $itemCategory, TenantManager $tenantManager): View
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();
        $parentCategories = ItemCategory::where('company_id', $companyId)
            ->where('id', '!=', $itemCategory->id)
            ->where('is_active', true)
            ->get();

        return view('item-categories.edit', compact('itemCategory', 'parentCategories'));
    }

    public function update(Request $request, ItemCategory $itemCategory): RedirectResponse
    {
        $this->authorize('item.manage');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:item_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $itemCategory->update($validated);

        return redirect()->route('item-categories.index')
            ->with('success', 'আইটেম ক্যাটাগরি সফলভাবে হালনাগাদ হয়েছে।');
    }

    public function destroy(ItemCategory $itemCategory): RedirectResponse
    {
        $this->authorize('item.manage');

        if ($itemCategory->items()->exists()) {
            return back()->with('error', 'এই ক্যাটাগরিতে উপাদান/আইটেম সংযুক্ত রয়েছে, মুছে ফেলা যাবে না।');
        }

        $itemCategory->delete();

        return redirect()->route('item-categories.index')
            ->with('success', 'ক্যাটাগরি সফলভাবে মুছে ফেলা হয়েছে।');
    }
}
