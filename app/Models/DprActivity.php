<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DprActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_progress_report_id',
        'boq_item_id',
        'activity_name',
        'location',
        'quantity_completed',
        'unit',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity_completed' => 'decimal:4',
        ];
    }

    public function dailyProgressReport(): BelongsTo
    {
        return $this->belongsTo(DailyProgressReport::class);
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(BoqItem::class);
    }
}
