<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('item.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();

        $units = Unit::query()
            ->when($companyId, fn ($q) => $q->where(fn ($sub) => $sub->where('company_id', $companyId)->orWhereNull('company_id')))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('symbol', 'like', "%{$search}%");
                });
            })
            ->withCount('items')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('units.index', compact('units', 'search'));
    }

    public function create(): View
    {
        $this->authorize('item.manage');

        return view('units.create');
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('item.manage');

        $companyId = $tenantManager->currentCompanyId();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'name_bn' => ['nullable', 'string', 'max:50'],
            'symbol' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:weight,volume,area,length,count,unit'],
            'is_active' => ['boolean'],
        ]);

        $validated['company_id'] = $companyId;
        $validated['is_active'] = $request->boolean('is_active', true);

        Unit::create($validated);

        return redirect()->route('units.index')
            ->with('success', 'পরিমাপের একক সফলভাবে সংরক্ষণ করা হয়েছে।');
    }

    public function edit(Unit $unit): View
    {
        $this->authorize('item.manage');

        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $this->authorize('item.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'name_bn' => ['nullable', 'string', 'max:50'],
            'symbol' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:weight,volume,area,length,count,unit'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', 'পরিমাপের একক সফলভাবে হালনাগাদ করা হয়েছে।');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $this->authorize('item.manage');

        if ($unit->items()->exists()) {
            return back()->with('error', 'এই এককের অধীনে মালামাল যুক্ত রয়েছে, তাই মুছে ফেলা সম্ভব নয়।');
        }

        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'পরিমাপের একক মুছে ফেলা হয়েছে।');
    }
}
