<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use LaravelJsonApi\Eloquent\SoftDeletes;

/**
 * App\Models\LocationType
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|LocationType newModelQuery()
 * @method static Builder|LocationType newQuery()
 * @method static Builder|LocationType query()
 * @method static Builder|LocationType whereCreatedAt($value)
 * @method static Builder|LocationType whereCustomerId($value)
 * @method static Builder|LocationType whereDeletedAt($value)
 * @method static Builder|LocationType whereId($value)
 * @method static Builder|LocationType whereName($value)
 * @method static Builder|LocationType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Customer $customer
 * @property int|null $pickable
 * @property int|null $sellable
 * @method static Builder|LocationType wherePickable($value)
 * @method static Builder|LocationType whereSellable($value)
 */
class LocationType extends Model
{
    use HasFactory, SoftDeletes;

    public const SELLABLE_NO = 0;
    public const SELLABLE_YES = 1;
    public const SELLABLE_NOT_SET = 2;

    public const PICKABLE_NO = 0;
    public const PICKABLE_YES = 1;
    public const PICKABLE_NOT_SET = 2;

    public const DISABLED_ON_PICKING_APP_NO = 0;
    public const DISABLED_ON_PICKING_APP_YES = 1;
    public const DISABLED_ON_PICKING_APP_NOT_SET = 2;

    protected $fillable = [
        'customer_id',
        'pickable',
        'sellable',
        'name',
        'disabled_on_picking_app'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function isPickable(): bool
    {
        return (bool) $this->pickable;
    }

    public function isSellable(): bool
    {
        return (bool) $this->sellable;
    }
}
