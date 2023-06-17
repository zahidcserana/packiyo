<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ShipmentTracking
 *
 * @property int $id
 * @property int $shipment_id
 * @property string|null $tracking_number
 * @property string|null $tracking_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Shipment $shipment
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShipmentTracking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereTrackingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereTrackingUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShipmentTracking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShipmentTracking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShipmentTracking withoutTrashed()
 * @mixin \Eloquent
 */
class ShipmentTracking extends Model
{
    use SoftDeletes;

    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_RETURN = 'return';

    protected $fillable = [
        'shipment_id',
        'tracking_number',
        'tracking_url',
        'type'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class)->withTrashed();
    }
}
