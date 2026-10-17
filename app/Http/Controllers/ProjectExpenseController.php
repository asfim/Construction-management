<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\CostCode;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectExpenseController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('budget.view');

        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();
        $costCodeId = $request->integer('cost_code_id');

        $expenses = ProjectExpense::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->when($costCodeId, fn ($q) => $q->where('cost_code_id', $costCodeId))
            ->with(['project', 'costCode', 'creator'])
            ->latest('expense_date')
            ->paginate(15)
            ->withQueryString();

        $costCodes = CostCode::where('company_id', $companyId)->where('is_active', true)->get();
        $totalExpenseAmount = ProjectExpense::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->sum('amount');

        return view('project-expenses.index', compact('expenses', 'costCodes', 'costCodeId', 'totalExpenseAmount'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('budget.manage');

        $companyId = $tenantManager->currentCompanyId();
        $activeProjectId = $tenantManager->currentProjectId();

        $projects = Project::where('company_id', $companyId)->where('status', 'active')->get();
        $costCodes = CostCode::where('company_id', $companyId)->where('is_active', true)->get();
        $existingVouchers = ProjectExpense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('voucher_number', 'LIKE', 'EXP-' . date('Ymd') . '-%')
            ->pluck('voucher_number')
            ->toArray();
        $maxVoucherSeq = 0;
        foreach ($existingVouchers as $num) {
            if (preg_match('/EXP-\d+-(\d+)/', $num, $matches)) {
                $seq = (int) $matches[1];
                if ($seq > $maxVoucherSeq) $maxVoucherSeq = $seq;
            }
        }
        $nextVoucherNo = sprintf('EXP-%s-%03d', date('Ymd'), $maxVoucherSeq + 1);

        return view('project-expenses.create', compact('projects', 'costCodes', 'activeProjectId', 'nextVoucherNo'));
    }

    public function store(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $this->authorize('budget.manage');

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'cost_code_id' => 'required|exists:cost_codes,id',
            'voucher_number' => 'required|string|max:50',
            'expense_date' => 'required|date',
            'payee_name' => 'required|string|max:255',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,bkash_nagad',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'reference_no' => 'nullable|string|max:100',
        ]);

        $companyId = $tenantManager->currentCompanyId();

        $expense = ProjectExpense::create([
            'company_id' => $companyId,
            'project_id' => $validated['project_id'],
            'cost_code_id' => $validated['cost_code_id'],
            'voucher_number' => $validated['voucher_number'],
            'expense_date' => $validated['expense_date'],
            'payee_name' => $validated['payee_name'],
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'reference_no' => $validated['reference_no'] ?? null,
            'status' => 'paid',
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Automatically update actual_amount in the project's budget line if one exists
        $budget = Budget::where('project_id', $validated['project_id'])->first();
        if ($budget) {
            $budgetLine = $budget->lines()->where('cost_code_id', $validated['cost_code_id'])->first();
            if ($budgetLine) {
                $budgetLine->actual_amount += (float) $validated['amount'];
                $budgetLine->save();
            }
        }

        return redirect()->route('project-expenses.index')
            ->with('success', "Expense voucher {$expense->voucher_number} recorded successfully.");
    }

    public function show(ProjectExpense $projectExpense): View
    {
        $this->authorize('budget.view');

        $projectExpense->load(['project', 'costCode', 'creator']);

        return view('project-expenses.show', compact('projectExpense'));
    }
}
