<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('item.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();
        $categoryId = $request->input('category_id');

        $items = Item::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->with(['category', 'unit'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = ItemCategory::where('is_active', true)->get();

        return view('items.index', compact('items', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        $this->authorize('item.manage');

        $categories = ItemCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('items.create', compact('categories', 'units'));
    }

    public function store(StoreItemRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $validated['company_id'] = $tenantManager->currentCompanyId();

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', __('erp.messages.created_successfully'));
    }

    public function edit(Item $item): View
    {
        $this->authorize('item.manage');

        $categories = ItemCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('items.edit', compact('item', 'categories', 'units'));
    }

    public function update(StoreItemRequest $request, Item $item): RedirectResponse
    {
        $item->update($request->validated());

        return redirect()->route('items.index')
            ->with('success', __('erp.messages.updated_successfully'));
    }

    public function destroy(Item $item): RedirectResponse
    {
        $this->authorize('item.manage');

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', __('erp.messages.deleted_successfully'));
    }
}
