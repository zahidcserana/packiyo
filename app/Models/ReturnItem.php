<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReturnItem
 *
 * @property int $id
 * @property int $return_id
 * @property int $product_id
 * @property float $quantity
 * @property float $quantity_received
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 * @property-read Return_ $return_
 * @method static bool|null forceDelete()
 * @method static Builder|ReturnItem newModelQuery()
 * @method static Builder|ReturnItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReturnItem onlyTrashed()
 * @method static Builder|ReturnItem query()
 * @method static bool|null restore()
 * @method static Builder|ReturnItem whereCreatedAt($value)
 * @method static Builder|ReturnItem whereDeletedAt($value)
 * @method static Builder|ReturnItem whereId($value)
 * @method static Builder|ReturnItem whereProductId($value)
 * @method static Builder|ReturnItem whereQuantity($value)
 * @method static Builder|ReturnItem whereQuantityReceived($value)
 * @method static Builder|ReturnItem whereReturnId($value)
 * @method static Builder|ReturnItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReturnItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReturnItem withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 */
class ReturnItem extends Model
{
    use RevisionableTrait;

    use SoftDeletes;

    protected $fillable = [
        'return_id',
        'product_id',
        'quantity',
        'quantity_received'
    ];

    public function return_()
    {
        return $this->belongsTo(Return_::class, 'return_id', 'id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
