<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    /**
     * Switch current active company.
     */
    public function switchCompany(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        $company = Company::findOrFail($validated['company_id']);
        $user = $request->user();

        if (! $tenantManager->userHasAccessToCompany($user, $company->id)) {
            return back()->with('error', __('erp.messages.unauthorized_action'));
        }

        $tenantManager->setCompany($company);

        // Reset project if previous project belongs to a different company
        $currentProject = $tenantManager->currentProject();
        if ($currentProject && $currentProject->company_id !== $company->id) {
            $tenantManager->setProject(null);
        }

        return back()->with('success', __('erp.messages.switched_company'));
    }

    /**
     * Switch current active project.
     */
    public function switchProject(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable'],
        ]);

        $projectId = $validated['project_id'];

        if (empty($projectId) || $projectId === 'all') {
            $tenantManager->setProject(null);
            return back()->with('success', __('erp.messages.switched_project'));
        }

        $project = Project::withoutGlobalScopes()->findOrFail($projectId);
        $user = $request->user();

        if (! $tenantManager->userHasAccessToProject($user, $project->id)) {
            return back()->with('error', __('erp.messages.unauthorized_action'));
        }

        // If project belongs to different company, switch company too if user has access
        if ($project->company_id !== $tenantManager->currentCompanyId()) {
            if ($tenantManager->userHasAccessToCompany($user, $project->company_id)) {
                $tenantManager->setCompany($project->company_id);
            }
        }

        $tenantManager->setProject($project);

        return back()->with('success', __('erp.messages.switched_project'));
    }
}
