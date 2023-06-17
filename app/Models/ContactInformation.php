<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Venturecraft\Revisionable\RevisionableTrait;
use Webpatser\Countries\Countries;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * App\Models\ContactInformation
 *
 * @property int $id
 * @property string $name
 * @property string|null $company_name
 * @property string|null $company_number
 * @property string $address
 * @property string|null $address2
 * @property string $zip
 * @property string $city
 * @property string $email
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $billingOrders
 * @property-read int|null $billing_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Customer[] $customers
 * @property-read int|null $customers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $shippingOrders
 * @property-read int|null $shipping_orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Supplier[] $suppliers
 * @property-read int|null $suppliers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Warehouse[] $warehouses
 * @property-read int|null $warehouses_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContactInformation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereCompanyNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContactInformation whereZip($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContactInformation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ContactInformation withoutTrashed()
 * @mixin \Eloquent
 * @property string $object_type
 * @property int $object_id
 * @property-read \App\Models\Order|null $billingOrder
 * @property-read Model|\Eloquent $object
 * @property-read \App\Models\Order|null $shippingOrder
 * @method static \Illuminate\Database\Eloquent\Builder|ContactInformation whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactInformation whereObjectType($value)
 * @property int $country_id
 * @property-read Countries $country
 * @method static \Illuminate\Database\Eloquent\Builder|ContactInformation whereCountryId($value)
 */
class ContactInformation extends Model implements AuditableInterface
{
    use HasFactory, SoftDeletes, RevisionableTrait, AuditableTrait;

    protected $fillable = [
        'name',
        'company_name',
        'company_number',
        'address',
        'address2',
        'zip',
        'city',
        'state',
        'country_id',
        'email',
        'phone'
    ];

    protected $table = 'contact_informations';

    /**
     * Audit configs
     */
    protected $auditStrict = true;

    protected $auditEvents = [
        'updated' => 'getUpdatedEventAttributes'
    ];

    protected $auditInclude = [
        'name',
        'company_name',
        'company_number',
        'address',
        'address2',
        'zip',
        'city',
        'state',
        'country_id',
        'email',
        'phone',
    ];

    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    public function object()
    {
        return $this->morphTo();
    }

    public function shippingOrder()
    {
        return $this->hasOne(Order::class, 'shipping_contact_information_id', 'id');
    }

    public function billingOrder()
    {
        return $this->hasOne(Order::class, 'billing_contact_information_id', 'id');
    }

    /**
     * Get the old/new attributes of a created event.
     *
     * @return array
     */
    public function getUpdatedEventAttributes(): array
    {
        $relationName = '';
        if ($this->object->shipping_contact_information_id === $this->id) {
            $relationName = 'shipping_contact_information';
        } elseif ($this->object->billing_contact_information_id === $this->id) {
            $relationName = 'billing_contact_information';
        }

        $old = [];
        $new = [];

        if (in_array(AuditableTrait::class, class_uses_recursive(get_class($this->object)))) {
            foreach ($this->getDirty() as $attribute => $value) {
                if ($this->isAttributeAuditable($attribute)) {
                    $old[$relationName][$attribute] = Arr::get($this->original, $attribute);
                    $new[$relationName][$attribute] = Arr::get($this->attributes, $attribute);
                }
            }
        }

        return [
            $old,
            $new,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['custom_message'] = '';

        if (Arr::has($data, 'new_values.shipping_contact_information.country_id')) {
            $data['old_values']['shipping_contact_information']['country'] = Countries::find($this->getOriginal('country_id'))->name ?? '';
            $data['new_values']['shipping_contact_information']['country'] = Countries::find($this->getAttribute('country_id'))->name ?? '';
        }

        if (Arr::has($data, 'new_values.billing_contact_information.country_id')) {
            $data['old_values']['billing_contact_information']['country'] = Countries::find($this->getOriginal('country_id'))->name ?? '';
            $data['new_values']['billing_contact_information']['country'] = Countries::find($this->getAttribute('country_id'))->name ?? '';
        }

        if ($this->auditEvent == 'updated') {
            if (Arr::has($data, 'new_values.shipping_contact_information')) {
                $exceptData = Arr::except($data, ['new_values.shipping_contact_information.country_id']);
                $contactInformationArr = Arr::get($exceptData, 'new_values.shipping_contact_information');
                $data['custom_message'] .= 'Shipping:<br/>';
            } elseif (Arr::has($data, 'new_values.billing_contact_information')) {
                $exceptData = Arr::except($data, ['new_values.billing_contact_information.country_id']);
                $contactInformationArr = Arr::get($exceptData, 'new_values.billing_contact_information');
                $data['custom_message'] .= 'Billing:<br/>';
            }

            foreach ($contactInformationArr as $attribute => $value) {
                if (empty($this->getOriginal($attribute))) {
                    $data['custom_message'] .= __(':attribute set to <em>":new"</em> <br/>', [
                        'attribute' => str_replace('_', ' ', ucfirst($attribute)),
                        'new' => $attribute == 'country' ? $this->getAttribute($attribute)->name : $this->getAttribute($attribute)
                    ]);
                } else {
                    $data['custom_message'] .= __(':attribute changed from <em>":old"</em> to <em>":new"</em> <br/>', [
                        'attribute' => str_replace('_', ' ', ucfirst($attribute)),
                        'old' => $this->getOriginal($attribute),
                        'new' => $attribute == 'country' ? $this->getAttribute($attribute)->name : $this->getAttribute($attribute)
                    ]);
                }
            }
        }

        return $data;
    }
}

