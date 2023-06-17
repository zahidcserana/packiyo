<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingBox extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'name',
        'length',
        'width',
        'height',
        'height_locked',
        'length_locked',
        'width_locked'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

}
