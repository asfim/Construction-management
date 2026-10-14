<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RateAnalysis extends Model
{
    use HasFactory, BelongsToCompany, Auditable;

    protected $fillable = [
        'company_id',
        'boq_item_id',
        'item_id',
        'title',
        'unit',
        'material_cost',
        'labor_cost',
        'equipment_cost',
        'overhead_percentage',
        'profit_percentage',
        'wastage_percentage',
        'total_unit_rate',
        'status',
        'created_by',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'material_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
            'equipment_cost' => 'decimal:2',
            'overhead_percentage' => 'decimal:2',
            'profit_percentage' => 'decimal:2',
            'wastage_percentage' => 'decimal:2',
            'total_unit_rate' => 'decimal:2',
        ];
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(RateAnalysisComponent::class);
    }

    /**
     * Compute total rate based on engineering breakdown:
     * Base = (Material * (1 + Wastage%)) + Labor + Equipment
     * Total = Base * (1 + (Overhead% + Profit%))
     */
    public function recalculate(): void
    {
        $material = (float) $this->components()->where('component_type', 'material')->sum('amount');
        $labor = (float) $this->components()->where('component_type', 'labor')->sum('amount');
        $equipment = (float) $this->components()->whereIn('component_type', ['equipment', 'transport'])->sum('amount');

        $this->material_cost = $material;
        $this->labor_cost = $labor;
        $this->equipment_cost = $equipment;

        $wastageFactor = 1 + ($this->wastage_percentage / 100);
        $baseWithWastage = ($material * $wastageFactor) + $labor + $equipment;

        $markupFactor = 1 + (($this->overhead_percentage + $this->profit_percentage) / 100);
        $this->total_unit_rate = round($baseWithWastage * $markupFactor, 2);

        $this->saveQuietly();
    }
}
