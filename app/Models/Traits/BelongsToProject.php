<?php

namespace App\Models\Traits;

use App\Models\Project;
use App\Models\Scopes\ProjectScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToProject
{
    /**
     * Boot the trait to optionally apply project scope and set project_id on model creation.
     */
    protected static function bootBelongsToProject(): void
    {
        static::addGlobalScope(new ProjectScope);

        static::creating(function ($model) {
            if (empty($model->project_id)) {
                $tenantManager = app(TenantManager::class);
                $projectId = $tenantManager->currentProjectId();
                if ($projectId) {
                    $model->project_id = $projectId;
                }
            }
        });
    }

    /**
     * Relationship to Project.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
