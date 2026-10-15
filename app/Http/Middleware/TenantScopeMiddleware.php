<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Project;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeMiddleware
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user) {
            // Verify or set active company
            $currentCompanyId = $this->tenantManager->currentCompanyId();

            if (! $currentCompanyId) {
                // Pick user's default company or first company
                $firstCompany = $user->companies()->first() ?? ($user->hasRole('Super Admin') ? Company::first() : null);
                if ($firstCompany) {
                    $this->tenantManager->setCompany($firstCompany);
                    $currentCompanyId = $firstCompany->id;
                }
            } else {
                // Verify user has access
                if (! $this->tenantManager->userHasAccessToCompany($user, $currentCompanyId)) {
                    $firstCompany = $user->companies()->first();
                    if ($firstCompany) {
                        $this->tenantManager->setCompany($firstCompany);
                        $currentCompanyId = $firstCompany->id;
                    } else if (! $user->hasRole('Super Admin')) {
                        abort(403, 'Unauthorized company access.');
                    }
                }
            }

            // Get active project
            $currentProjectId = $this->tenantManager->currentProjectId();
            if ($currentProjectId && ! $this->tenantManager->userHasAccessToProject($user, $currentProjectId)) {
                $this->tenantManager->setProject(null);
                $currentProjectId = null;
            }

            // Sync active project if visiting a project-specific route
            $routeProject = $request->route('project');
            if ($routeProject) {
                $targetProject = $routeProject instanceof Project ? $routeProject : Project::find($routeProject);
                if ($targetProject && $this->tenantManager->userHasAccessToProject($user, $targetProject->id)) {
                    if ($targetProject->company_id && $targetProject->company_id !== $currentCompanyId && $this->tenantManager->userHasAccessToCompany($user, $targetProject->company_id)) {
                        $this->tenantManager->setCompany($targetProject->company_id);
                        $currentCompanyId = $targetProject->company_id;
                    }
                    if ($currentProjectId !== $targetProject->id) {
                        $this->tenantManager->setProject($targetProject->id);
                        $currentProjectId = $targetProject->id;
                    }
                }
            }

            // Available companies for user
            $userCompanies = $user->hasRole('Super Admin')
                ? Company::where('is_active', true)->get()
                : $user->companies()->where('is_active', true)->get();

            // Available projects for user under current company
            $projectsQuery = Project::query();
            if ($currentCompanyId) {
                $projectsQuery->where('company_id', $currentCompanyId);
            }

            if (! $user->hasRole(['Super Admin', 'Company Owner', 'Managing Director'])) {
                $projectsQuery->whereHas('members', function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                });
            }

            $userProjects = $projectsQuery->get();

            // Share with all views
            View::share('currentCompany', $this->tenantManager->currentCompany());
            View::share('currentProject', $this->tenantManager->currentProject());
            View::share('userCompanies', $userCompanies);
            View::share('userProjects', $userProjects);
        }

        return $next($request);
    }
}
