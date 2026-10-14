<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\BelongsToProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientBill extends Model
{
    use HasFactory, BelongsToCompany, BelongsToProject, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'bill_number',
        'bill_date',
        'period_start',
        'period_end',
        'gross_amount',
        'advance_recovery_percentage',
        'advance_recovery_amount',
        'retention_percentage',
        'retention_amount',
        'vat_percentage',
        'vat_amount',
        'net_payable',
        'status',
        'created_by',
        'certified_by',
        'certified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'bill_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_amount' => 'decimal:2',
            'advance_recovery_percentage' => 'decimal:2',
            'advance_recovery_amount' => 'decimal:2',
            'retention_percentage' => 'decimal:2',
            'retention_amount' => 'decimal:2',
            'vat_percentage' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'net_payable' => 'decimal:2',
            'certified_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ClientBillItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function certifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'certified_by');
    }
}
