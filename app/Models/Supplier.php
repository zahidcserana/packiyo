<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Supplier
 *
 * @property int $id
 * @property int $customer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ContactInformation $contactInformation
 * @property-read Customer $customer
 * @property-read Collection|PurchaseOrder[] $purchaseOrders
 * @property-read int|null $purchase_orders_count
 * @method static bool|null forceDelete()
 * @method static Builder|Supplier newModelQuery()
 * @method static Builder|Supplier newQuery()
 * @method static \Illuminate\Database\Query\Builder|Supplier onlyTrashed()
 * @method static Builder|Supplier query()
 * @method static bool|null restore()
 * @method static Builder|Supplier whereContactInformationId($value)
 * @method static Builder|Supplier whereCreatedAt($value)
 * @method static Builder|Supplier whereCustomerId($value)
 * @method static Builder|Supplier whereDeletedAt($value)
 * @method static Builder|Supplier whereId($value)
 * @method static Builder|Supplier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Supplier withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Supplier withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $currency
 * @property string|null $internal_note
 * @property string|null $default_purchase_order_note
 * @property-read Collection|\App\Models\Product[] $products
 * @property-read int|null $products_count
 * @method static Builder|Supplier whereCurrency($value)
 * @method static Builder|Supplier whereDefaultPurchaseOrderNote($value)
 * @method static Builder|Supplier whereInternalNote($value)
 */
class Supplier extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $cascadeDeletes = [
        'contactInformation'
    ];

    protected $fillable = [
        'customer_id',
        'currency',
        'internal_note',
        'default_purchase_order_note'
    ];

    public function contactInformation()
    {
        return $this->morphOne(ContactInformation::class, 'object');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
