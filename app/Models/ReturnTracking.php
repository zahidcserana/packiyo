<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReturnTracking
 *
 * @property-read Return_|null $return
 * @method static Builder|ReturnTracking newModelQuery()
 * @method static Builder|ReturnTracking newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReturnTracking onlyTrashed()
 * @method static Builder|ReturnTracking query()
 * @method static \Illuminate\Database\Query\Builder|ReturnTracking withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReturnTracking withoutTrashed()
 * @mixin \Eloquent
 */
class ReturnTracking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_id',
        'tracking_number',
        'tracking_url'
    ];

    public function return()
    {
        return $this->belongsTo(Return_::class)->withTrashed();
    }
}
