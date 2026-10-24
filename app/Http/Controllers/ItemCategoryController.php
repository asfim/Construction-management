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
        $type = $request->string('type', 'all')->trim()->value();
        $parentId = $request->integer('parent_id') ?: null;

        $baseQuery = ItemCategory::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId));

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'parent' => (clone $baseQuery)->whereNull('parent_id')->count(),
            'sub' => (clone $baseQuery)->whereNotNull('parent_id')->count(),
        ];

        $categories = (clone $baseQuery)
            ->when($type === 'parent', fn ($q) => $q->whereNull('parent_id'))
            ->when($type === 'sub', fn ($q) => $q->whereNotNull('parent_id'))
            ->when($parentId, fn ($q) => $q->where('parent_id', $parentId))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->with(['parent', 'children', 'items'])
            ->withCount(['items', 'children'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $parentCategories = ItemCategory::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        return view('item-categories.index', compact('categories', 'search', 'type', 'parentId', 'counts', 'parentCategories'));
    }

    public function create(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();
        $parentCategories = ItemCategory::where('company_id', $companyId)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        $selectedParentId = $request->integer('parent_id') ?: null;
        $defaultType = $request->get('type', $selectedParentId ? 'sub' : 'parent');

        return view('item-categories.create', compact('parentCategories', 'selectedParentId', 'defaultType'));
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
            'category_type' => ['nullable', 'string', 'in:parent,sub'],
            'parent_id' => ['nullable', 'exists:item_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = $request->boolean('is_active', true);

        // If explicitly set as a Main/Parent Category, parent_id is always null
        if (($request->input('category_type') === 'parent') || empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
        }

        ItemCategory::create($validated);

        return redirect()->route('item-categories.index')
            ->with('success', 'Item category created successfully.');
    }

    public function edit(ItemCategory $itemCategory, TenantManager $tenantManager): View
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();
        $parentCategories = ItemCategory::where('company_id', $companyId)
            ->where('id', '!=', $itemCategory->id)
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->get();

        $currentType = $itemCategory->parent_id ? 'sub' : 'parent';

        return view('item-categories.edit', compact('itemCategory', 'parentCategories', 'currentType'));
    }

    public function update(Request $request, ItemCategory $itemCategory): RedirectResponse
    {
        $this->authorize('item.manage');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'name_bn' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'category_type' => ['nullable', 'string', 'in:parent,sub'],
            'parent_id' => ['nullable', 'exists:item_categories,id'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // If explicitly set as a Main/Parent Category, parent_id is always null
        if (($request->input('category_type') === 'parent') || empty($validated['parent_id'])) {
            $validated['parent_id'] = null;
        }

        $itemCategory->update($validated);

        return redirect()->route('item-categories.index')
            ->with('success', 'Item category updated successfully.');
    }

    public function destroy(ItemCategory $itemCategory): RedirectResponse
    {
        $this->authorize('item.manage');

        if ($itemCategory->items()->exists()) {
            return back()->with('error', 'Cannot delete category because items are associated with it.');
        }

        $itemCategory->delete();

        return redirect()->route('item-categories.index')
            ->with('success', 'Item category deleted successfully.');
    }
}
