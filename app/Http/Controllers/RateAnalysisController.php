<?php

namespace App\Http\Controllers;

use App\Models\BoqItem;
use App\Models\Item;
use App\Models\RateAnalysis;
use App\Models\RateAnalysisComponent;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RateAnalysisController extends Controller
{
    public function show(BoqItem $boqItem, TenantManager $tenantManager): View
    {
        $this->authorize('boq.view');

        $boqItem->load(['boqVersion.project', 'costCode']);

        $rateAnalysis = RateAnalysis::firstOrCreate(
            ['boq_item_id' => $boqItem->id],
            [
                'company_id' => $tenantManager->currentCompanyId(),
                'title' => "Rate Analysis for {$boqItem->item_code}",
                'unit' => $boqItem->unit,
                'total_unit_rate' => $boqItem->unit_rate,
                'overhead_percentage' => 5.00,
                'profit_percentage' => 10.00,
                'wastage_percentage' => 3.00,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]
        );

        $rateAnalysis->load('components.item');
        $availableItems = Item::where('is_active', true)->get();

        return view('boqs.rate-analysis', compact('boqItem', 'rateAnalysis', 'availableItems'));
    }

    public function storeComponent(Request $request, RateAnalysis $rateAnalysis): RedirectResponse
    {
        $this->authorize('boq.manage');

        $validated = $request->validate([
            'component_type' => ['required', 'in:material,labor,equipment,transport,other'],
            'item_id' => ['nullable', 'exists:items,id'],
            'description' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'rate' => ['required', 'numeric', 'min:0'],
        ]);

        $rateAnalysis->components()->create($validated);

        return back()->with('success', 'কম্পোনেন্ট সফলভাবে যোগ করা হয়েছে।');
    }

    public function updateParameters(Request $request, RateAnalysis $rateAnalysis): RedirectResponse
    {
        $this->authorize('boq.manage');

        $validated = $request->validate([
            'overhead_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'profit_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'wastage_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $rateAnalysis->update($validated);
        $rateAnalysis->recalculate();

        // Optionally update BOQ item unit rate if BOQ version is not locked
        if (! $rateAnalysis->boqItem->boqVersion->is_locked) {
            $rateAnalysis->boqItem->update(['unit_rate' => $rateAnalysis->total_unit_rate]);
        }

        return back()->with('success', 'রেট অ্যানালাইসিস প্যারামিটার ও ইউনিট রেট সফলভাবে হালনাগাদ করা হয়েছে।');
    }
}
