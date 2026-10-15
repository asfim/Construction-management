<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Branch;
use App\Models\Project;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request, TenantManager $tenantManager): View
    {
        $this->authorize('project.view');

        $companyId = $tenantManager->currentCompanyId();
        $search = $request->string('search')->trim();
        $status = $request->string('status')->trim();

        $projects = Project::query()
            ->when($companyId, fn ($q) => $q->where('company_id', $companyId))
            ->when($status->isNotEmpty(), fn ($q) => $q->where('status', $status->value()))
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->with(['branch', 'projectManager'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $branches = Branch::where('is_active', true)->get();

        return view('projects.index', compact('projects', 'branches', 'search', 'status'));
    }

    public function create(TenantManager $tenantManager): View
    {
        $this->authorize('project.create');

        $companyId = $tenantManager->currentCompanyId();
        $branches = Branch::where('is_active', true)->get();
        $managers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Project Manager', 'Project Director', 'Company Owner']))->get();

        return view('projects.create', compact('branches', 'managers'));
    }

    public function store(StoreProjectRequest $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validated();
        $validated['company_id'] = $tenantManager->currentCompanyId();
        $validated['created_by'] = $request->user()->id;

        $project = Project::create($validated);

        // If project manager assigned, add to project_user pivot
        if (! empty($validated['project_manager_id'])) {
            $project->members()->syncWithoutDetaching([
                $validated['project_manager_id'] => [
                    'project_role' => 'project_manager',
                    'assigned_date' => now()->toDateString(),
                    'is_active' => true,
                ],
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', __('erp.messages.created_successfully'));
    }

    public function show(Project $project): View
    {
        $this->authorize('project.view');

        $project->load(['branch', 'projectManager', 'creator', 'members']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorize('project.edit');

        $branches = Branch::where('is_active', true)->get();
        $managers = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Project Manager', 'Project Director', 'Company Owner']))->get();

        return view('projects.edit', compact('project', 'branches', 'managers'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();
        $project->update($validated);

        if (! empty($validated['project_manager_id'])) {
            $project->members()->syncWithoutDetaching([
                $validated['project_manager_id'] => [
                    'project_role' => 'project_manager',
                    'assigned_date' => now()->toDateString(),
                    'is_active' => true,
                ],
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', __('erp.messages.updated_successfully'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('project.delete');

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', __('erp.messages.deleted_successfully'));
    }

    public function updateProgress(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('project.edit');

        $validated = $request->validate([
            'progress_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['nullable', 'in:planning,active,on_hold,completed,archived'],
        ]);

        $data = ['progress_percent' => $validated['progress_percent']];
        if (! empty($validated['status'])) {
            $data['status'] = $validated['status'];
        } elseif ((float) $validated['progress_percent'] >= 100 && $project->status !== 'completed') {
            $data['status'] = 'completed';
        }

        $project->update($data);

        return back()->with('success', "Progress for {$project->name} updated to {$project->progress_percent}%.");
    }
}
