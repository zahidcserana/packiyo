<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class ShippingMethod extends Model
{
    use HasFactory, SoftDeletes, RevisionableTrait;

    protected $fillable = [
        'shipping_carrier_id',
        'name',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function getCarrierNameAndNameAttribute()
    {
        return $this->shippingCarrier->name . ' - ' . $this->name;
    }

    public function shippingCarrier()
    {
        return $this->belongsTo(ShippingCarrier::class)->withTrashed();
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
