<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\PrintJob
 *
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $job_start
 * @property string|null $job_end
 * @property int $printer_id
 * @property string|null $job_id_system
 * @property string|null $status
 * @property int $user_id
 * @property-read Model|\Eloquent $object
 * @property-read \App\Models\Printer $printer
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob newQuery()
 * @method static \Illuminate\Database\Query\Builder|PrintJob onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereJobEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereJobIdSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereJobStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereObjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob wherePrinterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrintJob whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|PrintJob withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PrintJob withoutTrashed()
 * @mixin \Eloquent
 */
class PrintJob extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'print_jobs';

    protected $fillable = [
        'object_type',
        'object_id',
        'url',
        'type',
        'job_start',
        'job_end',
        'printer_id',
        'job_id_system',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    public function object()
    {
        return $this->morphTo();
    }
}
