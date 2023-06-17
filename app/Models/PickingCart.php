<?php

namespace App\Models;

use App\Traits\HasBarcodeTrait;
use Illuminate\Database\{Eloquent\Builder, Eloquent\Collection, Eloquent\Model, Eloquent\SoftDeletes};
use Illuminate\Support\Carbon;

/**
 * App\Models\Tote
 *
 * @property int $id
 * @property int $warehouse_id
 * @property string $name
 * @property string $barcode
 * @property int $number_of_totes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static Builder|PickingCart newModelQuery()
 * @method static Builder|PickingCart newQuery()
 * @method static \Illuminate\Database\Query\Builder|PickingCart onlyTrashed()
 * @method static Builder|PickingCart query()
 * @method static bool|null restore()
 * @method static Builder|PickingCart whereId($value)
 * @method static Builder|PickingCart whereBarcode($value)
 * @method static Builder|PickingCart whereName($value)
 * @method static Builder|PickingCart whereWarehouseId($value)
 * @method static Builder|PickingCart whereNumberOfTotes($value)
 * @method static Builder|PickingCart whereCreatedAt($value)
 * @method static Builder|PickingCart whereUpdatedAt($value)
 * @method static Builder|PickingCart whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PickingCart withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PickingCart withoutTrashed()
 * @mixin \Eloquent
 * @property-read Warehouse $warehouse
 * @property-read Collection|Tote[] $totes
 * @property-read int|null $totes_count
 */

class PickingCart extends Model
{
    use SoftDeletes, HasBarcodeTrait;

    protected $fillable = [
        'warehouse_id',
        'name',
        'barcode',
        'number_of_totes'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withTrashed();
    }

    public function totes()
    {
        return $this->hasMany(Tote::class)->withTrashed();
    }
}
