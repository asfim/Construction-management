<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\BelongsToProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyProgressReport extends Model
{
    use HasFactory, BelongsToCompany, BelongsToProject, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'dpr_number',
        'report_date',
        'weather',
        'site_engineer_id',
        'skilled_workers',
        'unskilled_workers',
        'total_workers',
        'work_performed',
        'equipment_deployed',
        'material_received_used',
        'issues_safety_notes',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'approved_at' => 'datetime',
            'skilled_workers' => 'integer',
            'unskilled_workers' => 'integer',
            'total_workers' => 'integer',
        ];
    }

    public function siteEngineer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'site_engineer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(DprActivity::class);
    }
}
