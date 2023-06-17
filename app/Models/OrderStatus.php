<?php

namespace App\Models;

use Database\Factories\OrderStatusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\OrderStatus
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @method static bool|null forceDelete()
 * @method static Builder|OrderStatus newModelQuery()
 * @method static Builder|OrderStatus newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderStatus onlyTrashed()
 * @method static Builder|OrderStatus query()
 * @method static bool|null restore()
 * @method static Builder|OrderStatus whereCreatedAt($value)
 * @method static Builder|OrderStatus whereCustomerId($value)
 * @method static Builder|OrderStatus whereDeletedAt($value)
 * @method static Builder|OrderStatus whereId($value)
 * @method static Builder|OrderStatus whereName($value)
 * @method static Builder|OrderStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderStatus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderStatus withoutTrashed()
 * @mixin \Eloquent
 * @property int $fulfilled
 * @property int $cancelled
 * @method static Builder|OrderStatus whereFulfilled($value)
 * @method static Builder|OrderStatus whereCancelled($value)
 * @property string|null $color
 * @method static OrderStatusFactory factory(...$parameters)
 * @method static Builder|OrderStatus whereColor($value)
 */
class OrderStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'color'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
