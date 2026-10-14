<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateAnalysisComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'rate_analysis_id',
        'component_type',
        'item_id',
        'description',
        'unit',
        'quantity',
        'rate',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $comp) {
            $comp->amount = bcmul((string) $comp->quantity, (string) $comp->rate, 2);
        });

        static::saved(function (self $comp) {
            $comp->rateAnalysis?->recalculate();
        });

        static::deleted(function (self $comp) {
            $comp->rateAnalysis?->recalculate();
        });
    }

    public function rateAnalysis(): BelongsTo
    {
        return $this->belongsTo(RateAnalysis::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
