<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ShipmentItem
 *
 * @property int $id
 * @property int $shipment_id
 * @property int $order_item_id
 * @property float $quantity
 * @property float $quantity_shipped
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\OrderItem $orderItem
 * @property-read \App\Models\Shipment $shipment
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShipmentItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereQuantityShipped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ShipmentItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShipmentItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ShipmentItem withoutTrashed()
 * @mixin \Eloquent
 */
class ShipmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shipment_id',
        'order_item_id',
        'quantity'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class)->withTrashed();
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class)->withTrashed();
    }
}
