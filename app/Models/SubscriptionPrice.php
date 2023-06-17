<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\SubscriptionPrice
 *
 * @property int $id
 * @property string $stripe_id
 * @property string $stripe_product_id
 * @property int $plan_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SubscriptionPrice newModelQuery()
 * @method static Builder|SubscriptionPrice newQuery()
 * @method static Builder|SubscriptionPrice query()
 * @method static Builder|SubscriptionPrice whereCreatedAt($value)
 * @method static Builder|SubscriptionPrice whereId($value)
 * @method static Builder|SubscriptionPrice wherePlanId($value)
 * @method static Builder|SubscriptionPrice whereStripeId($value)
 * @method static Builder|SubscriptionPrice whereStripeProductId($value)
 * @method static Builder|SubscriptionPrice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SubscriptionPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_id',
        'stripe_product_id',
        'plan_id'
    ];
}
