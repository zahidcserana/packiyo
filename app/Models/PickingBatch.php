<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PickingBatch
 *
 * @property int $id
 * @property int $customer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PickingBatchItem[] $pickingBatchItems
 * @property-read int|null $picking_batch_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch newQuery()
 * @method static \Illuminate\Database\Query\Builder|PickingBatch onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PickingBatch withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PickingBatch withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $picking_cart_id
 * @method static \Illuminate\Database\Eloquent\Builder|PickingBatch wherePickingCartId($value)
 * @property-read \App\Models\PickingCart|null $pickingCart
 */
class PickingBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'picking_cart_id',
        'type'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function pickingBatchItems()
    {
        return $this->hasMany(PickingBatchItem::class);
    }

    public function pickingBatchItemsWithTrashed()
    {
        return $this->hasMany(PickingBatchItem::class)->withTrashed();
    }

    public function pickingBatchItemsNotFinished()
    {
        return $this->pickingBatchItems()->whereColumn('quantity', '!=', 'quantity_picked');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function pickingCart()
    {
        return $this->belongsTo(PickingCart::class);
    }
}
