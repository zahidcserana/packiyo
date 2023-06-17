<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ShipmentLabel
 *
 * @property int $id
 * @property int $shipment_id
 * @property string $size
 * @property string|null $url
 * @property mixed|null $content
 * @property string|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Shipment $shipment
 * @method static Builder|ShipmentLabel newModelQuery()
 * @method static Builder|ShipmentLabel newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShipmentLabel onlyTrashed()
 * @method static Builder|ShipmentLabel query()
 * @method static Builder|ShipmentLabel whereContent($value)
 * @method static Builder|ShipmentLabel whereCreatedAt($value)
 * @method static Builder|ShipmentLabel whereDeletedAt($value)
 * @method static Builder|ShipmentLabel whereId($value)
 * @method static Builder|ShipmentLabel whereShipmentId($value)
 * @method static Builder|ShipmentLabel whereSize($value)
 * @method static Builder|ShipmentLabel whereType($value)
 * @method static Builder|ShipmentLabel whereUpdatedAt($value)
 * @method static Builder|ShipmentLabel whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|ShipmentLabel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShipmentLabel withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $document_type
 * @method static Builder|ShipmentLabel whereDocumentType($value)
 */
class ShipmentLabel extends Model
{
    use SoftDeletes;

    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_RETURN = 'return';

    protected $fillable = [
        'shipment_id',
        'size',
        'url',
        'content',
        'document_type',
        'type'
    ];

    protected $hidden = [
        'content'
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class)->withTrashed();
    }
}
