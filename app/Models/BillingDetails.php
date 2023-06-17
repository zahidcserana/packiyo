<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\BillingDetails
 *
 * @method static Builder|BillingDetails newModelQuery()
 * @method static Builder|BillingDetails newQuery()
 * @method static Builder|BillingDetails query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $account_holder_name
 * @property string|null $email
 * @property string|null $phone
 * @property string $address
 * @property string|null $address2
 * @property string $postal_code
 * @property string|null $city
 * @property string|null $state
 * @property int $customer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $country_id
 * @method static \Illuminate\Database\Query\Builder|BillingDetails onlyTrashed()
 * @method static Builder|BillingDetails whereAccountHolderName($value)
 * @method static Builder|BillingDetails whereAddress($value)
 * @method static Builder|BillingDetails whereAddress2($value)
 * @method static Builder|BillingDetails whereCity($value)
 * @method static Builder|BillingDetails whereCreatedAt($value)
 * @method static Builder|BillingDetails whereCustomerId($value)
 * @method static Builder|BillingDetails whereDeletedAt($value)
 * @method static Builder|BillingDetails whereEmail($value)
 * @method static Builder|BillingDetails whereId($value)
 * @method static Builder|BillingDetails wherePhone($value)
 * @method static Builder|BillingDetails wherePostalCode($value)
 * @method static Builder|BillingDetails whereState($value)
 * @method static Builder|BillingDetails whereUpdatedAt($value)
 * @method static Builder|BillingDetails whereCountryId($value)
 * @method static \Illuminate\Database\Query\Builder|BillingDetails withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BillingDetails withoutTrashed()
 * @property-read Customer $customer
 */
class BillingDetails extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

    protected $table = 'customer_billing_details';

    protected $fillable = [
        'customer_id',
        'account_holder_name',
        'address',
        'address2',
        'city',
        'country_id',
        'state',
        'phone',
        'email',
        'postal_code'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }
}
