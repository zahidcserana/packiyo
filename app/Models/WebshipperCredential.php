<?php

namespace App\Models;

use App\Interfaces\ShippingProviderCredential;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\WebshipperCredential
 *
 * @property int $id
 * @property int $customer_id
 * @property string $api_base_url
 * @property string $api_key
 * @property int $order_channel_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 * @method static Builder|WebshipperCredential newModelQuery()
 * @method static Builder|WebshipperCredential newQuery()
 * @method static \Illuminate\Database\Query\Builder|WebshipperCredential onlyTrashed()
 * @method static Builder|WebshipperCredential query()
 * @method static Builder|WebshipperCredential whereApiBaseUrl($value)
 * @method static Builder|WebshipperCredential whereApiKey($value)
 * @method static Builder|WebshipperCredential whereCreatedAt($value)
 * @method static Builder|WebshipperCredential whereCustomerId($value)
 * @method static Builder|WebshipperCredential whereDeletedAt($value)
 * @method static Builder|WebshipperCredential whereId($value)
 * @method static Builder|WebshipperCredential whereOrderChannelId($value)
 * @method static Builder|WebshipperCredential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|WebshipperCredential withTrashed()
 * @method static \Illuminate\Database\Query\Builder|WebshipperCredential withoutTrashed()
 * @mixin \Eloquent
 */
class WebshipperCredential extends Model implements ShippingProviderCredential
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'api_base_url',
        'api_key',
        'order_channel_id'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingMethods(): MorphMany
    {
        return $this->morphMany(ShippingMethod::class, 'credential');
    }
}
