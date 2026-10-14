<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class TenantManager
{
    protected const SESSION_COMPANY_KEY = 'active_company_id';
    protected const SESSION_PROJECT_KEY = 'active_project_id';

    /**
     * Get the active company ID for the current session.
     */
    public function currentCompanyId(): ?int
    {
        if (Session::has(self::SESSION_COMPANY_KEY)) {
            return (int) Session::get(self::SESSION_COMPANY_KEY);
        }

        $user = auth()->user();
        if ($user && $user->current_company_id) {
            Session::put(self::SESSION_COMPANY_KEY, $user->current_company_id);
            return (int) $user->current_company_id;
        }

        return null;
    }

    /**
     * Get the active company model.
     */
    public function currentCompany(): ?Company
    {
        $companyId = $this->currentCompanyId();
        if (! $companyId) {
            return null;
        }

        return Company::find($companyId);
    }

    /**
     * Set the active company for the authenticated user and session.
     */
    public function setCompany(Company|int $company): void
    {
        $companyId = is_numeric($company) ? (int) $company : $company->id;
        Session::put(self::SESSION_COMPANY_KEY, $companyId);

        $user = auth()->user();
        if ($user) {
            $user->updateQuietly(['current_company_id' => $companyId]);
        }
    }

    /**
     * Get the active project ID for the current session.
     */
    public function currentProjectId(): ?int
    {
        if (Session::has(self::SESSION_PROJECT_KEY)) {
            return (int) Session::get(self::SESSION_PROJECT_KEY);
        }

        $user = auth()->user();
        if ($user && $user->current_project_id) {
            Session::put(self::SESSION_PROJECT_KEY, $user->current_project_id);
            return (int) $user->current_project_id;
        }

        return null;
    }

    /**
     * Get the active project model.
     */
    public function currentProject(): ?Project
    {
        $projectId = $this->currentProjectId();
        if (! $projectId) {
            return null;
        }

        return Project::find($projectId);
    }

    /**
     * Set the active project for the authenticated user and session.
     */
    public function setProject(Project|int|null $project): void
    {
        if ($project === null) {
            Session::forget(self::SESSION_PROJECT_KEY);
            $user = auth()->user();
            if ($user) {
                $user->updateQuietly(['current_project_id' => null]);
            }
            return;
        }

        $projectId = is_numeric($project) ? (int) $project : $project->id;
        Session::put(self::SESSION_PROJECT_KEY, $projectId);

        $user = auth()->user();
        if ($user) {
            $user->updateQuietly(['current_project_id' => $projectId]);
        }
    }

    /**
     * Verify if the user has access to the given company.
     */
    public function userHasAccessToCompany(User $user, int $companyId): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return $user->companies()->where('companies.id', $companyId)->exists();
    }

    /**
     * Verify if the user has access to the given project.
     */
    public function userHasAccessToProject(User $user, int $projectId): bool
    {
        if ($user->hasRole(['Super Admin', 'Company Owner', 'Managing Director'])) {
            return true;
        }

        return $user->projects()->where('projects.id', $projectId)->exists();
    }
}
