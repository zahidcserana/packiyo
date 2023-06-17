<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, SoftDeletes};

class LotItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lot_id',
        'location_id',
        'quantity_added',
        'quantity_removed'
    ];

    public function lot()
    {
        return $this->belongsTo(Lot::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }
}
