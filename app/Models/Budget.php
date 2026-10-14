<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\BelongsToProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory, BelongsToCompany, BelongsToProject, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'boq_version_id',
        'budget_code',
        'title',
        'total_budget_amount',
        'contingency_amount',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'total_budget_amount' => 'decimal:2',
            'contingency_amount' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function boqVersion(): BelongsTo
    {
        return $this->belongsTo(BoqVersion::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BudgetLine::class);
    }

    public function recalculate(): void
    {
        $this->total_budget_amount = $this->lines()->sum('planned_amount');
        $this->saveQuietly();
    }
}
