<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CycleCountBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cycleCountBatchItems()
    {
        return $this->hasMany(CycleCountBatchItem::class);
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }
}
