<?php

namespace App\Models;

use App\Traits\HasBarcodeTrait;
use Database\Factories\LocationFactory;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $name
 * @property int $pickable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|InventoryLog[] $inventoryLogDestinations
 * @property-read int|null $inventory_log_destinations_count
 * @property-read Collection|InventoryLog[] $inventoryLogSources
 * @property-read int|null $inventory_log_sources_count
 * @property-read Collection|LocationProduct[] $products
 * @property-read int|null $products_count
 * @property-read Warehouse $warehouse
 * @method static bool|null forceDelete()
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static \Illuminate\Database\Query\Builder|Location onlyTrashed()
 * @method static Builder|Location query()
 * @method static bool|null restore()
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereDeletedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereName($value)
 * @method static Builder|Location wherePickable($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @method static Builder|Location whereWarehouseId($value)
 * @method static \Illuminate\Database\Query\Builder|Location withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Location withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $barcode
 * @property int $protected
 * @property int $sellable
 * @property-read Collection|InventoryLog[] $inventoryLogAssociatedObject
 * @property-read int|null $inventory_log_associated_object_count
 * @property-read Collection|ToteOrderItem[] $toteOrderItems
 * @property-read int|null $tote_order_items_count
 * @method static LocationFactory factory(...$parameters)
 * @method static Builder|Location whereBarcode($value)
 * @method static Builder|Location whereProtected($value)
 * @method static Builder|Location whereSellable($value)
 * @property int|null $location_type_id
 * @method static Builder|Location whereLocationTypeId($value)
 * @property-read LocationType|null $locationType
 * @property int $pickable_effective
 * @property string|null $priority_counting_requested_at
 * @property string|null $last_counted_at
 * @method static Builder|Location whereLastCountedAt($value)
 * @method static Builder|Location wherePickableEffective($value)
 * @method static Builder|Location wherePriorityCountingRequestedAt($value)
 */
class Location extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, HasBarcodeTrait;

    public const PROTECTED_LOC_NAME_RECEIVING = 'Receiving';
    public const PROTECTED_LOC_NAME_RESHIP = 'Reship';

    protected $cascadeDeletes = [
        'products'
    ];

    protected $fillable = [
        'warehouse_id',
        'name',
        'pickable',
        'pickable_effective',
        'sellable',
        'barcode',
        'location_type_id',
        'protected',
        'priority_counting_requested_at',
        'disabled_on_picking_app',
        'disabled_on_picking_app_effective'
    ];

    protected $attributes = [
        'pickable' => 0,
        'disabled_on_picking_app' => 0
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function products()
    {
        return $this->hasMany(LocationProduct::class);
    }

    public function inventoryLogAssociatedObject()
    {
        return $this->morphMany(InventoryLog::class, 'associated_object');
    }

    public function isPickable(): bool
    {
        if (is_null($this->locationType)) {
            return $this->pickable;
        }

        if (!is_null($this->locationType->pickable)) {
            return $this->locationType->pickable;
        }

        return $this->pickable;
    }

    public function isPickableLabel(): string
    {
        if (is_null($this->locationType)) {
            return $this->pickable ? 'YES' : 'NO';
        }

        if (!is_null($this->locationType->pickable)) {
            if ($this->pickable === $this->locationType->pickable) {
                return $this->pickable ? 'YES' : 'NO';
            }

            return ($this->pickable ? 'YES' : 'NO') . ', effective ' . ($this->locationType->pickable ? 'YES' : 'NO');
        }

        return $this->pickable ? 'YES' : 'NO';
    }

    public function isDisabledOnPickingAppLabel(): string
    {
        if (is_null($this->locationType)) {
            return $this->disabled_on_picking_app ? 'YES' : 'NO';
        }

        if (!is_null($this->locationType->disabled_on_picking_app)) {
            if ($this->disabled_on_picking_app === $this->locationType->disabled_on_picking_app) {
                return $this->disabled_on_picking_app ? 'YES' : 'NO';
            }

            return ($this->disabled_on_picking_app ? 'YES' : 'NO') . ', effective ' . ($this->locationType->disabled_on_picking_app ? 'YES' : 'NO');
        }

        return $this->disabled_on_picking_app ? 'YES' : 'NO';
    }

    public function isSellable(): bool
    {
        if (is_null($this->locationType)) {
            return (bool) $this->sellable;
        }

        if (!is_null($this->locationType->sellable)) {
            return (bool) $this->locationType->sellable;
        }

        return (bool) $this->sellable;
    }

    public function isSellableLabel(): string
    {
        if (is_null($this->locationType)) {
            return $this->sellable ? 'YES' : 'NO';
        }

        if (!is_null($this->locationType->sellable)) {
            if ($this->sellable === $this->locationType->sellable) {
                return $this->sellable ? 'YES' : 'NO';
            }

            return ($this->sellable ? 'YES' : 'NO') . ', effective ' . ($this->locationType->sellable ? 'YES' : 'NO');
        }

        return $this->sellable ? 'YES' : 'NO';
    }

    public function toteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class)->withoutTrashed();
    }

    public function locationType(): BelongsTo
    {
        return $this->belongsTo(LocationType::class);
    }

    public function lotItems()
    {
        return $this->hasMany(LotItem::class, 'location_id', 'id');
    }
}
