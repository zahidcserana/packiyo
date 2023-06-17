<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Webhook
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $customer_id
 * @property string $name
 * @property string $object_type
 * @property string $operation
 * @property string $url
 * @property string|null $secret_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook newQuery()
 * @method static \Illuminate\Database\Query\Builder|Webhook onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereOperation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereSecretKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Webhook whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Webhook withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Webhook withoutTrashed()
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use SoftDeletes;

    const OPERATION_TYPE_STORE = 'Store';
    const OPERATION_TYPE_UPDATE = 'Update';
    const OPERATION_TYPE_DESTROY = 'Destroy';
    const OPERATION_TYPE_RECEIVE = 'Receive';

    const WEBHOOK_OBJECT_TYPES = [
        'Contact Information' => ContactInformation::class,
        'Customer' => Customer::class,
        'Customer User' => CustomerUser::class,
        'Customer User Role' => CustomerUserRole::class,
        'Location' => Location::class,
        'Location Product' => LocationProduct::class,
        'Order' => Order::class,
        'Order Status' => OrderStatus::class,
        'Product' => Product::class,
        'Purchase Order' => PurchaseOrder::class,
        'Purchase Order Status' => PurchaseOrderStatus::class,
        'Returns' => Return_::class,
        'Role' => Role::class,
        'Shipment' => Shipment::class,
        'Supplier' => Supplier::class,
        'Task' => Task::class,
        'Task Type' => TaskType::class,
        'User' => User::class,
        'Warehouse' => Warehouse::class,
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'order_channel_id',
        'name',
        'object_type',
        'operation',
        'url',
        'secret_key'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderChannel()
    {
        return $this->belongsTo(OrderChannel::class);
    }
}
