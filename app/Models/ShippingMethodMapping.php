<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * App\Models\ShippingMethodMapping
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $shipping_method_id
 * @property int|null $return_shipping_method_id
 * @property string $shipping_method_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer|null $customer
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read ShippingMethod|null $shippingMethod
 * @method static \Database\Factories\ShippingMethodMappingFactory factory(...$parameters)
 * @method static Builder|ShippingMethodMapping newModelQuery()
 * @method static Builder|ShippingMethodMapping newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShippingMethodMapping onlyTrashed()
 * @method static Builder|ShippingMethodMapping query()
 * @method static Builder|ShippingMethodMapping whereCreatedAt($value)
 * @method static Builder|ShippingMethodMapping whereCustomerId($value)
 * @method static Builder|ShippingMethodMapping whereDeletedAt($value)
 * @method static Builder|ShippingMethodMapping whereId($value)
 * @method static Builder|ShippingMethodMapping whereReturnShippingMethodId($value)
 * @method static Builder|ShippingMethodMapping whereShippingMethodId($value)
 * @method static Builder|ShippingMethodMapping whereShippingMethodName($value)
 * @method static Builder|ShippingMethodMapping whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShippingMethodMapping withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShippingMethodMapping withoutTrashed()
 * @mixin \Eloquent
 */
class ShippingMethodMapping extends Model
{
    use HasFactory, SoftDeletes, RevisionableTrait;

    protected $fillable = [
        'customer_id',
        'shipping_method_id',
        'return_shipping_method_id',
        'shipping_method_name'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class)->withTrashed();
    }

    public function returnShippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'return_shipping_method_id', 'id')->withTrashed();
    }
}
