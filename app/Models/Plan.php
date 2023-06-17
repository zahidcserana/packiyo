<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name
 * @property string $stripe_id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Plan newModelQuery()
 * @method static Builder|Plan newQuery()
 * @method static Builder|Plan query()
 * @method static Builder|Plan whereCreatedAt($value)
 * @method static Builder|Plan whereDescription($value)
 * @method static Builder|Plan whereId($value)
 * @method static Builder|Plan whereName($value)
 * @method static Builder|Plan whereStripeId($value)
 * @method static Builder|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_id',
        'name',
        'description'
    ];
}
