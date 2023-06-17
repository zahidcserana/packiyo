<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'customer_id',
      'name'
    ];

    public function orders()
    {
        return $this->morphedByMany(Order::class, 'taggable');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }

    public function purchaseOrders()
    {
        return $this->morphedByMany(PurchaseOrder::class, 'taggable');
    }

    public function shippingMethod()
    {
        return $this->morphedByMany(ShippingMethod::class, 'taggable');
    }

    public function returns()
    {
        return $this->morphedByMany(Return_::class, 'taggable');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
