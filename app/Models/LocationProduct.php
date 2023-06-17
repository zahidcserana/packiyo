<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\LocationProduct
 *
 * @property int $id
 * @property int $product_id
 * @property int $location_id
 * @property int $quantity_on_hand
 * @property int $quantity_reserved_for_picking
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Location $location
 * @property-read \App\Models\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereQuantityOnHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LocationProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LocationProduct extends Pivot
{
    protected $fillable = [
        'product_id',
        'location_id',
        'quantity_on_hand',
        'quantity_reserved_for_picking'
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function calculateQuantityReservedForPicking()
    {
        $pickingBatchQuery = PickingBatchItem::query()
            ->join('order_items', 'order_items.id', '=', 'picking_batch_items.order_item_id')
            ->where('picking_batch_items.location_id', $this->location_id)
            ->where('order_items.product_id', $this->product_id);

        $pickingBatchItemQuantity = $pickingBatchQuery
            ->sum('picking_batch_items.quantity');

        $quantityRemoved = $pickingBatchQuery
            ->join('tote_order_items', 'picking_batch_items.id', '=', 'tote_order_items.picking_batch_item_id')
            ->sum('tote_order_items.quantity_removed');

        $this->quantity_reserved_for_picking = $pickingBatchItemQuantity - $quantityRemoved;
        $this->save();
    }
}
