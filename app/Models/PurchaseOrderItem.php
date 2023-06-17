<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Venturecraft\Revisionable\Revision;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PurchaseOrderItem
 *
 * @property int $id
 * @property int $purchase_order_id
 * @property int $product_id
 * @property int $location_id
 * @property float $quantity
 * @property float $quantity_received
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 * @property-read PurchaseOrder $purchaseOrder
 * @method static bool|null forceDelete()
 * @method static Builder|PurchaseOrderItem newModelQuery()
 * @method static Builder|PurchaseOrderItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderItem onlyTrashed()
 * @method static Builder|PurchaseOrderItem query()
 * @method static bool|null restore()
 * @method static Builder|PurchaseOrderItem whereCreatedAt($value)
 * @method static Builder|PurchaseOrderItem whereDeletedAt($value)
 * @method static Builder|PurchaseOrderItem whereId($value)
 * @method static Builder|PurchaseOrderItem whereProductId($value)
 * @method static Builder|PurchaseOrderItem wherePurchaseOrderId($value)
 * @method static Builder|PurchaseOrderItem whereQuantity($value)
 * @method static Builder|PurchaseOrderItem whereQuantityReceived($value)
 * @method static Builder|PurchaseOrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderItem withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property int $quantity_pending
 * @method static Builder|PurchaseOrderItem whereQuantityPending($value)
 * @property int $quantity_rejected
 * @method static Builder|PurchaseOrderItem whereQuantityRejected($value)
 * @property int $quantity_sell_ahead
 * @method static Builder|PurchaseOrderItem whereQuantityAllocatedSellAhead($value)
 * @property string|null $external_id
 * @property-read Location|null $location
 * @method static Builder|PurchaseOrderItem whereExternalId($value)
 * @method static Builder|PurchaseOrderItem whereLocationId($value)
 */
class PurchaseOrderItem extends Model
{
    use RevisionableTrait;

    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'external_id',
        'product_id',
        'quantity',
        'quantity_received',
        'quantity_pending',
        'quantity_sell_ahead'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }
}
