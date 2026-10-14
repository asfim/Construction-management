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

class GoodsReceipt extends Model
{
    use HasFactory, BelongsToCompany, BelongsToProject, Auditable;

    protected $fillable = [
        'company_id',
        'project_id',
        'purchase_order_id',
        'warehouse_id',
        'grn_number',
        'received_date',
        'delivery_challan_no',
        'supplier_invoice_no',
        'status',
        'received_by',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GrnItem::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Approve GRN, post inventory stock transactions, and update PO delivery progress.
     */
    public function approveAndPostStock(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $this->update([
                'status' => 'approved',
                'verified_by' => $userId,
                'verified_at' => now(),
            ]);

            foreach ($this->items as $grnItem) {
                // 1. Update PO item received quantity
                $poItem = $grnItem->poItem;
                if ($poItem) {
                    $poItem->quantity_received = (float) $poItem->quantity_received + (float) $grnItem->quantity_accepted;
                    $poItem->save();
                }

                // 2. Post to Inventory Transaction Ledger
                InventoryTransaction::create([
                    'company_id' => $this->company_id,
                    'project_id' => $this->project_id,
                    'warehouse_id' => $this->warehouse_id,
                    'item_id' => $grnItem->item_id,
                    'transaction_type' => 'grn_in',
                    'quantity' => $grnItem->quantity_accepted,
                    'unit_cost' => $grnItem->unit_price,
                    'total_cost' => bcmul((string) $grnItem->quantity_accepted, (string) $grnItem->unit_price, 2),
                    'reference_type' => self::class,
                    'reference_id' => $this->id,
                    'transaction_date' => $this->received_date,
                    'notes' => "Challan #{$this->delivery_challan_no} (PO #{$this->purchaseOrder?->po_number})",
                    'created_by' => $userId,
                ]);
            }

            // 3. Update PO status
            $po = $this->purchaseOrder;
            if ($po) {
                $hasPending = $po->items()->get()->contains(function ($item) {
                    return (float) $item->quantity_received < (float) $item->quantity_ordered;
                });

                $po->status = $hasPending ? 'partially_received' : 'received';
                $po->save();
            }
        });
    }
}
