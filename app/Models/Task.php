<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int $user_id
 * @property int $task_type_id
 * @property string $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\TaskType $taskType
 * @property-read \App\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereTaskTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Task whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Task withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $taskable_type
 * @property int|null $taskable_id
 * @property int|null $customer_id
 * @property-read \App\Models\Customer|null $customer
 * @property-read Model|\Eloquent $taskable
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTaskableType($value)
 */
class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'task_type_id',
        'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class)->withTrashed();
    }

    public function taskable()
    {
        return $this->morphTo();
    }
}