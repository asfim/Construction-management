<?php

namespace App\Http\Controllers;

use App\Models\CostCode;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostCodeController extends Controller
{
    public function index(TenantManager $tenantManager): View
    {
        $this->authorize('cost-code.view');

        $companyId = $tenantManager->currentCompanyId();
        $costCodes = CostCode::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->with(['parent', 'children'])
            ->whereNull('parent_id')
            ->get();

        return view('cost-codes.index', compact('costCodes'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('cost-code.manage');

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'name_bn' => ['nullable', 'string', 'max:150'],
            'parent_id' => ['nullable', 'exists:cost_codes,id'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['company_id'] = $tenantManager->currentCompanyId();

        CostCode::create($validated);

        return redirect()->route('cost-codes.index')
            ->with('success', __('erp.messages.created_successfully'));
    }
}
