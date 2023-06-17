<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TaskType
 *
 * @property int $id
 * @property int $customer_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Customer $customer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaskType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TaskType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaskType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\TaskType withoutTrashed()
 * @mixin \Eloquent
 * @property int $type
 * @method static \Illuminate\Database\Eloquent\Builder|TaskType whereType($value)
 */
class TaskType extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'type'
    ];

    const TYPE_GENERIC = 0;
    const TYPE_PICKING = 1;
    const TYPE_PACKING = 2;
    const TYPE_COUNTING_PRODUCTS = 3;
    const TYPE_COUNTING_LOCATIONS = 4;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
