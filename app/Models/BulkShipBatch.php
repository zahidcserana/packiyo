<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkShipBatch extends Model
{
    protected $guarded = [];

    protected $dates = ['shipped_at'];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot(
            'labels_merged',
            'shipment_id',
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function printedUser()
    {
        return $this->belongsTo(User::class, 'printed_user_id');
    }

    public function packedUser()
    {
        return $this->belongsTo(User::class, 'packed_user_id');
    }
}
