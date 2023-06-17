<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CycleCountBatchItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cycle_count_batch_id',
        'location_id',
        'product_id',
        'quantity'
    ];

    public function CycleCountBatch()
    {
        return $this->belongsTo(CycleCountBatch::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
