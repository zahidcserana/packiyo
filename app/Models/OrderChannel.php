<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use LaravelJsonApi\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class OrderChannel extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, RevisionableTrait;

    protected $revisionCreationsEnabled = true;

    protected $fillable = [
        'customer_id',
        'name'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }
}
