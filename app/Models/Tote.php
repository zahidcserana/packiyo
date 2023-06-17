<?php

namespace App\Models;

use App\Traits\HasBarcodeTrait;
use App\Traits\HasUniqueIdentifierSuggestionTrait;
use Illuminate\Database\{Eloquent\Builder, Eloquent\Collection, Eloquent\Model, Eloquent\SoftDeletes};
use Illuminate\Support\Carbon;

/**
 * App\Models\Tote
 *
 * @property int $id
 * @property int $warehouse_id
 * @property int|null $order_id
 * @property int|null $picking_cart_id
 * @property string $name
 * @property string $barcode
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static Builder|Tote newModelQuery()
 * @method static Builder|Tote newQuery()
 * @method static \Illuminate\Database\Query\Builder|Tote onlyTrashed()
 * @method static Builder|Tote query()
 * @method static bool|null restore()
 * @method static Builder|Tote whereId($value)
 * @method static Builder|Tote whereBarcode($value)
 * @method static Builder|Tote whereName($value)
 * @method static Builder|Tote whereWarehouseId($value)
 * @method static Builder|Tote whereOrderId($value)
 * @method static Builder|Tote wherePickingCartId($value)
 * @method static Builder|Tote whereCreatedAt($value)
 * @method static Builder|Tote whereUpdatedAt($value)
 * @method static Builder|Tote whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Tote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Tote withoutTrashed()
 * @property-read Collection|ToteOrderItem[] $toteOrderItems
 * @property-read int|null $tote_order_items_count
 * @property-read Warehouse $warehouse
 * @property-read Order|null $order
 * @property-read PickingCart|null $pickingCart
 * @mixin \Eloquent
 */

class Tote extends Model
{
    use SoftDeletes, HasBarcodeTrait, HasUniqueIdentifierSuggestionTrait;

    public static $uniqueIdentifierColumn = 'name';
    public static $uniqueIdentifierReferenceColumn = 'warehouse_id';

    protected $fillable = [
        'warehouse_id',
        'order_id',
        'picking_cart_id',
        'name',
        'barcode'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function pickingCart()
    {
        return $this->belongsTo(PickingCart::class)->withTrashed();
    }

    public function toteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class)->withTrashed();
    }

    public function placedToteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class)->whereRaw('quantity_remaining > 0')->withTrashed();
    }
}
