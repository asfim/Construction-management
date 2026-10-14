<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use App\Models\Traits\BelongsToCompany;
use App\Models\Traits\BelongsToProject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StockIssue extends Model
{
    use HasFactory, BelongsToCompany, BelongsToProject, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'warehouse_id',
        'issue_no',
        'issue_date',
        'issued_to_contractor_or_labor',
        'cost_code_id',
        'status',
        'issued_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function costCode(): BelongsTo
    {
        return $this->belongsTo(CostCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockIssueItem::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Approve stock issue and record negative out transactions.
     */
    public function approveAndDeductStock(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $this->update([
                'status' => 'approved',
                'issued_by' => $userId,
            ]);

            foreach ($this->items as $issueItem) {
                // Post negative inventory transaction
                InventoryTransaction::create([
                    'company_id' => $this->company_id,
                    'project_id' => $this->project_id,
                    'warehouse_id' => $this->warehouse_id,
                    'item_id' => $issueItem->item_id,
                    'transaction_type' => 'issue_out',
                    'quantity' => -1 * abs((float) $issueItem->quantity_issued),
                    'unit_cost' => $issueItem->unit_cost,
                    'total_cost' => bcmul((string) $issueItem->quantity_issued, (string) $issueItem->unit_cost, 2),
                    'reference_type' => self::class,
                    'reference_id' => $this->id,
                    'transaction_date' => $this->issue_date,
                    'notes' => "Store Issue #{$this->issue_no} to {$this->issued_to_contractor_or_labor}",
                    'created_by' => $userId,
                ]);
            }
        });
    }
}
