<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'mr_item_id',
        'item_id',
        'quantity_ordered',
        'quantity_received',
        'unit_price',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered' => 'decimal:4',
            'quantity_received' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $item) {
            $item->total_amount = bcmul((string) $item->quantity_ordered, (string) $item->unit_price, 2);
        });

        static::saved(function (self $item) {
            $item->purchaseOrder?->recalculateTotals();
        });

        static::deleted(function (self $item) {
            $item->purchaseOrder?->recalculateTotals();
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function mrItem(): BelongsTo
    {
        return $this->belongsTo(MrItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GrnItem::class);
    }

    public function getPendingQuantityAttribute(): float
    {
        return max(0, (float) ($this->quantity_ordered - $this->quantity_received));
    }
}
