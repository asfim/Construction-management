<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\CostCode;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function show(Project $project, TenantManager $tenantManager): View
    {
        $this->authorize('budget.view');

        $budget = Budget::where('project_id', $project->id)
            ->with(['lines.costCode', 'creator', 'approver', 'boqVersion'])
            ->first();

        if (! $budget) {
            $budget = Budget::create([
                'company_id' => $tenantManager->currentCompanyId(),
                'project_id' => $project->id,
                'budget_code' => "BDG-{$project->code}",
                'title' => "Master Project Cost Control Budget for {$project->name}",
                'total_budget_amount' => $project->budget_amount,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);
        }

        $costCodes = CostCode::where('is_active', true)->get();

        $totalPlanned = $budget->lines->sum('planned_amount');
        $totalCommitted = $budget->lines->sum('committed_amount');
        $totalActual = $budget->lines->sum('actual_amount');
        $totalForecast = $budget->lines->sum('forecast_amount');
        $totalVariance = $totalPlanned - $totalActual;

        return view('budgets.show', compact(
            'project',
            'budget',
            'costCodes',
            'totalPlanned',
            'totalCommitted',
            'totalActual',
            'totalForecast',
            'totalVariance'
        ));
    }

    public function storeLine(Request $request, Budget $budget): RedirectResponse
    {
        $this->authorize('budget.manage');

        $validated = $request->validate([
            'cost_code_id' => ['required', 'exists:cost_codes,id'],
            'planned_amount' => ['required', 'numeric', 'min:0'],
            'forecast_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validated['forecast_amount'] = $validated['forecast_amount'] ?? $validated['planned_amount'];

        $budget->lines()->updateOrCreate(
            ['cost_code_id' => $validated['cost_code_id']],
            $validated
        );

        $budget->recalculate();

        return back()->with('success', 'বাজেট লাইন সফলভাবে হালনাগাদ করা হয়েছে।');
    }
}
