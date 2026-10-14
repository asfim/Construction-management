<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot auditable events.
     */
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAuditLog('created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newValues = $model->getDirty();
            if (! empty($newValues)) {
                $model->recordAuditLog('updated', $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('deleted', $model->getOriginal(), null);
        });
    }

    /**
     * Record an audit log entry for this model.
     */
    public function recordAuditLog(string $action, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): void
    {
        try {
            $user = auth()->user();
            $tenantManager = app(TenantManager::class);

            $companyId = $this->company_id ?? $tenantManager->currentCompanyId();
            $projectId = $this->project_id ?? $tenantManager->currentProjectId();

            AuditLog::create([
                'company_id' => $companyId,
                'project_id' => $projectId,
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'System',
                'action' => $action,
                'auditable_type' => get_class($this),
                'auditable_id' => $this->getKey(),
                'description' => $description ?? "{$action} " . class_basename($this) . " #{$this->getKey()}",
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Do not break execution if audit logging fails
            report($e);
        }
    }
}
