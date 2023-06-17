<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\PurchaseOrderStatus
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 * @method static bool|null forceDelete()
 * @method static Builder|PurchaseOrderStatus newModelQuery()
 * @method static Builder|PurchaseOrderStatus newQuery()
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderStatus onlyTrashed()
 * @method static Builder|PurchaseOrderStatus query()
 * @method static bool|null restore()
 * @method static Builder|PurchaseOrderStatus whereCreatedAt($value)
 * @method static Builder|PurchaseOrderStatus whereCustomerId($value)
 * @method static Builder|PurchaseOrderStatus whereDeletedAt($value)
 * @method static Builder|PurchaseOrderStatus whereId($value)
 * @method static Builder|PurchaseOrderStatus whereName($value)
 * @method static Builder|PurchaseOrderStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderStatus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PurchaseOrderStatus withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|PurchaseOrder[] $purchaseOrder
 * @property-read int|null $purchase_order_count
 */

class PurchaseOrderStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function purchaseOrder(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
