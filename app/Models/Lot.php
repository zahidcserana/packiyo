<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Factories\HasFactory, Model, SoftDeletes};

class Lot extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'expiration_date',
        'customer_id',
        'product_id',
        'supplier_id'
    ];

    public const FEFO_ID = 1;
    public const FIFO_ID = 2;

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function lotItems()
    {
        return $this->hasMany(LotItem::class)->withTrashed();
    }

    public function packageOrderItems()
    {
        return $this->hasMany(PackageOrderItem::class)->withTrashed();
    }

    public function getNameAndExpirationDateAndSupplierNameAttribute()
    {
        return $this->name . ' ' . user_date_time($this->exiration_date) . ' ' . $this->supplier->contactInformation->name;
    }
}
