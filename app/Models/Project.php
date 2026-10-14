<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name',
        'description',
        'client_name',
        'client_contact_person',
        'client_phone',
        'client_email',
        'site_address',
        'city',
        'division',
        'contract_value',
        'budget_amount',
        'retention_percentage',
        'advance_recovery_percentage',
        'start_date',
        'end_date',
        'revised_end_date',
        'status',
        'progress_percent',
        'project_manager_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'contract_value' => 'decimal:2',
            'budget_amount' => 'decimal:2',
            'retention_percentage' => 'decimal:2',
            'advance_recovery_percentage' => 'decimal:2',
            'progress_percent' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'revised_end_date' => 'date',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot(['project_role', 'assigned_date', 'is_active'])
            ->withTimestamps();
    }

    public function boqVersions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BoqVersion::class);
    }

    public function budget(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Budget::class);
    }

    public function dailyProgressReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DailyProgressReport::class);
    }

    public function clientBills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ClientBill::class);
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
