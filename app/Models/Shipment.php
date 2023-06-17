<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use \Venturecraft\Revisionable\RevisionableTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Shipment
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $shipping_method_id
 * @property int $processing_status
 * @property string|null $external_shipment_id
 * @property int|null $drop_point_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $packing_slip
 * @property Carbon|null $voided_at
 * @property string|null $external_manifest_id
 * @property-read Collection|\App\Models\BulkShipBatch[] $bulkShipBatch
 * @property-read int|null $bulk_ship_batch_count
 * @property-read \App\Models\ContactInformation|null $contactInformation
 * @property-read \App\Models\Order $order
 * @property-read Collection|\App\Models\Package[] $packages
 * @property-read int|null $packages_count
 * @property-read \App\Models\PrintJob|null $printJobs
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read Collection|\App\Models\ShipmentItem[] $shipmentItems
 * @property-read int|null $shipment_items_count
 * @property-read Collection|\App\Models\ShipmentLabel[] $shipmentLabels
 * @property-read int|null $shipment_labels_count
 * @property-read Collection|\App\Models\ShipmentTracking[] $shipmentTrackings
 * @property-read int|null $shipment_trackings_count
 * @property-read \App\Models\ShippingMethod|null $shippingMethod
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment newQuery()
 * @method static Builder|Shipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereDropPointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereExternalManifestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereExternalShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment wherePackingSlip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereProcessingStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereShippingMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereVoidedAt($value)
 * @method static Builder|Shipment withTrashed()
 * @method static Builder|Shipment withoutTrashed()
 * @mixin \Eloquent
 */
class Shipment extends Model
{
    use SoftDeletes, CascadeSoftDeletes, RevisionableTrait;

    const PROCESSING_STATUS_PENDING = 0;
    const PROCESSING_STATUS_IN_PROGRESS = 1;
    const PROCESSING_STATUS_SUCCESS = 2;
    const PROCESSING_STATUS_FAILED = 3;

    protected $cascadeDeletes = [
        'shipmentItems',
        'shipmentLabels',
        'shipmentTrackings',
    ];

    protected $fillable = [
        'order_id',
        'shipping_method_id',
        'user_id',
        'processing_status',
        'external_shipment_id',
        'shipping_method_id',
        'drop_point_id',
        'packing_slip'
    ];

    protected $dates = [
        'voided_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class)->withTrashed();
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function shipmentLabels()
    {
        return $this->hasMany(ShipmentLabel::class);
    }

    public function shipmentTrackings()
    {
        return $this->hasMany(ShipmentTracking::class);
    }

    public function contactInformation()
    {
        return $this->morphOne(ContactInformation::class, 'object');
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function printJobs()
    {
        return $this->morphOne(PrintJob::class, 'object')->withTrashed();
    }

    public function bulkShipBatch()
    {
        return $this->belongsToMany(BulkShipBatch::class, 'bulk_ship_batch_order')->withPivot('shipped', 'errors', 'shipment_id', 'labels_merged');
    }
}
