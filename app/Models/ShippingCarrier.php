<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class ShippingCarrier extends Model
{
    use HasFactory, SoftDeletes, RevisionableTrait;

    protected $fillable = [
        'customer_id',
        'carrier_service',
        'name',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function credential()
    {
        return $this->morphTo();
    }

    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class);
    }
}
