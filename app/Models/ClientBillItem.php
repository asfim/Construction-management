<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_bill_id',
        'boq_item_id',
        'description',
        'unit',
        'unit_rate',
        'previous_quantity',
        'current_quantity',
        'total_quantity',
        'current_amount',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'unit_rate' => 'decimal:2',
            'previous_quantity' => 'decimal:4',
            'current_quantity' => 'decimal:4',
            'total_quantity' => 'decimal:4',
            'current_amount' => 'decimal:2',
        ];
    }

    public function clientBill(): BelongsTo
    {
        return $this->belongsTo(ClientBill::class);
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }
}
