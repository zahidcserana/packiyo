<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ReturnLabel
 *
 * @property-read Return_|null $return
 * @method static Builder|ReturnLabel newModelQuery()
 * @method static Builder|ReturnLabel newQuery()
 * @method static \Illuminate\Database\Query\Builder|ReturnLabel onlyTrashed()
 * @method static Builder|ReturnLabel query()
 * @method static \Illuminate\Database\Query\Builder|ReturnLabel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ReturnLabel withoutTrashed()
 * @mixin \Eloquent
 */
class ReturnLabel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'return_id',
        'size',
        'url',
        'content',
        'type'
    ];

    protected $hidden = [
        'content'
    ];

    public function return()
    {
        return $this->belongsTo(Return_::class)->withTrashed();
    }
}
