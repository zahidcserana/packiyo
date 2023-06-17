<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'shipping_box_id',
        'shipping_method_id',
        'weight',
        'length',
        'width',
        'height',
        'shipment_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class)->withTrashed();
    }

    public function shippingBox()
    {
        return $this->hasOne(ShippingBox::class)->withTrashed();
    }

    public function packageOrderItems()
    {
        return $this->hasMany(PackageOrderItem::class);
    }
}
