<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\InventoryLog
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $associated_object_id
 * @property string $associated_object_type
 * @property int|null $location_id
 * @property string $source_type
 * @property int $source_id
 * @property string $destination_type
 * @property int $destination_id
 * @property int $product_id
 * @property int $new_on_hand
 * @property int $previous_on_hand
 * @property int $quantity
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $associatedObject
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|InventoryLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereAssociatedObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereAssociatedObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereDestinationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereDestinationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereNewOnHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog wherePreviousOnHand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereSourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InventoryLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|InventoryLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InventoryLog withoutTrashed()
 * @mixin \Eloquent
 */
class InventoryLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'location_id',
        'previous_on_hand',
        'new_on_hand',
        'quantity',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function associatedObject()
    {
        return $this->morphTo()->withTrashed();
    }

    public function getAssociatedObjectName()
    {
        if (!$this->associatedObject) {
            return '';
        }

        return $this->associatedObject->name ?? $this->associatedObject->number ?? $this->associatedObject->order->number ?? __('Item doesnt exist or is deleted !');
    }

    public function getReasonText()
    {
        return $this->reason . ' ' . $this->getAssociatedObjectName();
    }

}
