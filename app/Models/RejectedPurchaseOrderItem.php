<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LaravelJsonApi\Eloquent\SoftDeletes;

/**
 * App\Models\RejectedPurchaseOrderItem
 *
 * @property int $id
 * @property int $purchase_order_item_id
 * @property int $quantity
 * @property string $reason
 * @property string|null $note
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|RejectedPurchaseOrderItem newModelQuery()
 * @method static Builder|RejectedPurchaseOrderItem newQuery()
 * @method static Builder|RejectedPurchaseOrderItem query()
 * @method static Builder|RejectedPurchaseOrderItem whereCreatedAt($value)
 * @method static Builder|RejectedPurchaseOrderItem whereDeletedAt($value)
 * @method static Builder|RejectedPurchaseOrderItem whereId($value)
 * @method static Builder|RejectedPurchaseOrderItem whereNote($value)
 * @method static Builder|RejectedPurchaseOrderItem wherePurchaseOrderItemId($value)
 * @method static Builder|RejectedPurchaseOrderItem whereQuantity($value)
 * @method static Builder|RejectedPurchaseOrderItem whereReason($value)
 * @method static Builder|RejectedPurchaseOrderItem whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read PurchaseOrderItem $purchaseOrderItem
 */
class RejectedPurchaseOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_item_id',
        'quantity',
        'reason',
        'note'
    ];

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}
