<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_item_id',
        'package_id',
        'quantity',
        'serial_number',
        'location_id',
        'tote_id',
        'quantity',
        'lot_id'
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class)->withTrashed();
    }

    public function package()
    {
        return $this->belongsTo(Package::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function tote()
    {
        return $this->belongsTo(Tote::class)->withTrashed();
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class)->withTrashed();
    }
}
