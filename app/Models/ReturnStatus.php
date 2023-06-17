<?php

namespace App\Models;

use Database\Factories\ReturnStatusFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReturnStatus
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $color
 * @property-read Customer $customer
 * @property-read Collection|Return_[] $returns
 * @property-read int|null $returns_count
 * @method static ReturnStatusFactory factory(...$parameters)
 * @method static Builder|ReturnStatus newModelQuery()
 * @method static Builder|ReturnStatus newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReturnStatus onlyTrashed()
 * @method static Builder|ReturnStatus query()
 * @method static Builder|ReturnStatus whereColor($value)
 * @method static Builder|ReturnStatus whereCreatedAt($value)
 * @method static Builder|ReturnStatus whereCustomerId($value)
 * @method static Builder|ReturnStatus whereDeletedAt($value)
 * @method static Builder|ReturnStatus whereId($value)
 * @method static Builder|ReturnStatus whereName($value)
 * @method static Builder|ReturnStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ReturnStatus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReturnStatus withoutTrashed()
 * @mixin \Eloquent
 */
class ReturnStatus extends Model
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

    public function returns(): HasMany
    {
        return $this->hasMany(Return_::class);
    }
}
