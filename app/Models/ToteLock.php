<?php

namespace App\Models;

use Illuminate\Database\{Eloquent\Builder,
    Eloquent\Model,
    Eloquent\Relations\BelongsTo,
    Eloquent\SoftDeletes};
use Illuminate\Support\Carbon;


/**
 * App\Models\ToteLock
 *
 * @property int $id
 * @property int $tote_id
 * @property int $order_id
 * @property int|null $lock_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ToteLock newModelQuery()
 * @method static Builder|ToteLock newQuery()
 * @method static \Illuminate\Database\Query\Builder|ToteLock onlyTrashed()
 * @method static Builder|ToteLock query()
 * @method static Builder|ToteLock whereCreatedAt($value)
 * @method static Builder|ToteLock whereId($value)
 * @method static Builder|ToteLock whereLockType($value)
 * @method static Builder|ToteLock whereOrderId($value)
 * @method static Builder|ToteLock whereToteId($value)
 * @method static Builder|ToteLock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ToteLock withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ToteLock withoutTrashed()
 * @mixin \Eloquent
 * @property-read Order $order
 * @property-read Tote $tote
 */

class ToteLock extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tote_id',
        'order_id',
        'lock_type'
    ];

    public const LOCK_TYPE_PICKING = 0;
    public const LOCK_TYPE_PACKING = 1;
    public const LOCK_TYPE_SHIPPING = 2;

    public function tote(): BelongsTo
    {
        return $this->belongsTo(Tote::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
