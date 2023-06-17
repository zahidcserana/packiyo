<?php

namespace App\Models;

use Illuminate\Database\{Eloquent\Builder, Eloquent\Model, Eloquent\SoftDeletes};
use Illuminate\Support\Carbon;

/**
 * App\Models\ToteOrderItem
 *
 * @property int $id
 * @property int $tote_id
 * @property int $order_item_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $picked_at
 * @property string|null $removed_at
 * @property int $quantity
 * @property int $quantity_removed
 * @property int $quantity_remaining
 * @property int|null $location_id
 * @property int $picking_batch_item_id
 * @property int|null $user_id
 * @property-read \App\Models\Location|null $location
 * @property-read \App\Models\OrderItem $orderItem
 * @property-read \App\Models\PickingBatchItem $pickingBatchItem
 * @property-read \App\Models\Tote $tote
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|ToteOrderItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem wherePickedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem wherePickingBatchItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereQuantityRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereQuantityRemoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereRemovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereToteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ToteOrderItem whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ToteOrderItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ToteOrderItem withoutTrashed()
 * @mixin \Eloquent
 */
class ToteOrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'picking_batch_item_id',
        'order_item_id',
        'picked_at',
        'removed_at',
        'tote_id',
        'location_id',
        'quantity',
        'quantity_removed',
        'quantity_remaining'
    ];

    public function tote()
    {
        return $this->belongsTo(Tote::class)->withTrashed();
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function pickingBatchItem()
    {
        return $this->belongsTo(PickingBatchItem::class)->withTrashed();
    }
}
