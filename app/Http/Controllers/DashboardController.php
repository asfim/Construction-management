<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(TenantManager $tenantManager): View
    {
        $companyId = $tenantManager->currentCompanyId();
        $projectId = $tenantManager->currentProjectId();

        $projectsQuery = Project::query();
        if ($companyId) {
            $projectsQuery->where('company_id', $companyId);
        }

        if ($projectId) {
            $projectsQuery->where('id', $projectId);
        }

        $allProjects = (clone $projectsQuery)->get();

        $activeProjectsCount = $allProjects->where('status', 'active')->count();
        $totalContractValue = $allProjects->sum('contract_value');
        $totalBudgetAmount = $allProjects->sum('budget_amount');

        $teamCount = User::whereHas('projects', function ($q) use ($companyId, $projectId) {
            if ($companyId) {
                $q->where('projects.company_id', $companyId);
            }
            if ($projectId) {
                $q->where('projects.id', $projectId);
            }
        })->count();

        $recentProjects = (clone $projectsQuery)
            ->with(['branch', 'projectManager'])
            ->latest()
            ->take(6)
            ->get();

        $auditLogs = AuditLog::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->latest('created_at')
            ->take(8)
            ->get();

        $statusBreakdown = [
            'active' => $allProjects->where('status', 'active')->count(),
            'planning' => $allProjects->where('status', 'planning')->count(),
            'on_hold' => $allProjects->where('status', 'on_hold')->count(),
            'completed' => $allProjects->where('status', 'completed')->count(),
        ];

        return view('dashboard.index', compact(
            'activeProjectsCount',
            'totalContractValue',
            'totalBudgetAmount',
            'teamCount',
            'recentProjects',
            'auditLogs',
            'statusBreakdown'
        ));
    }
}
