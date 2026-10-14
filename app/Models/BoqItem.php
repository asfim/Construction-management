<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BoqItem extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'boq_version_id',
        'project_id',
        'cost_code_id',
        'item_code',
        'description',
        'unit',
        'planned_quantity',
        'unit_rate',
        'total_amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'planned_quantity' => 'decimal:4',
            'unit_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->total_amount = bcmul((string) $item->planned_quantity, (string) $item->unit_rate, 2);
        });

        static::saved(function (self $item) {
            $item->boqVersion?->recalculateTotal();
        });

        static::deleted(function (self $item) {
            $item->boqVersion?->recalculateTotal();
        });
    }

    public function boqVersion(): BelongsTo
    {
        return $this->belongsTo(BoqVersion::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function rateAnalysis(): HasOne
    {
        return $this->hasOne(RateAnalysis::class);
    }
}
