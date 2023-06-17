<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PickingBatchItem
 *
 * @property int $id
 * @property int $picking_batch_id
 * @property int $order_item_id
 * @property int $location_id
 * @property float $quantity
 * @property float $quantity_picked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\OrderItem $orderItem
 * @property-read \App\Models\PickingBatch $pickingBatch
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|PickingBatchItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem wherePickingBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereQuantityPicked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatchItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PickingBatchItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PickingBatchItem withoutTrashed()
 * @mixin \Eloquent
 */
class PickingBatchItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'picking_batch_id',
        'order_item_id',
        'location_id',
        'quantity',
        'quantity_picked',
    ];

    public function pickingBatch()
    {
        return $this->belongsTo(PickingBatch::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function toteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
