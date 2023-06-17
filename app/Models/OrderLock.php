<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OrderLock
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $lock_type
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderLock onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereLockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderLock whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|OrderLock withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderLock withoutTrashed()
 * @mixin \Eloquent
 */
class OrderLock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'lock_type',
        'user_id'
    ];

    const LOCK_TYPE_PICKING = 0;
    const LOCK_TYPE_PACKING = 1;
    const LOCK_TYPE_SHIPPING = 2;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
