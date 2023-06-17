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
 * App\Models\EasypostCredential
 *
 * @property int $id
 * @property int $customer_id
 * @property string $api_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Customer $customer
 * @method static Builder|EasypostCredential newModelQuery()
 * @method static Builder|EasypostCredential newQuery()
 * @method static \Illuminate\Database\Query\Builder|EasypostCredential onlyTrashed()
 * @method static Builder|EasypostCredential query()
 * @method static Builder|EasypostCredential whereApiKey($value)
 * @method static Builder|EasypostCredential whereCreatedAt($value)
 * @method static Builder|EasypostCredential whereCustomerId($value)
 * @method static Builder|EasypostCredential whereDeletedAt($value)
 * @method static Builder|EasypostCredential whereId($value)
 * @method static Builder|EasypostCredential whereOrderChannelId($value)
 * @method static Builder|EasypostCredential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EasypostCredential withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EasypostCredential withoutTrashed()
 * @mixin \Eloquent
 */
class EasypostCredential extends Model implements ShippingProviderCredential
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'api_key',
        'customs_signer',
        'contents_type',
        'contents_explanation',
        'eel_pfc'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function shippingCarriers(): MorphMany
    {
        return $this->morphMany(ShippingCarrier::class, 'credential');
    }
}
