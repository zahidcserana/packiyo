<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{

    use SoftDeletes, CascadeSoftDeletes;

    protected $table = 'printers';

    protected $cascadeDeletes = [
       'printJobs'
    ];

    protected $fillable = [
        'hostname',
        'name',
        'user_id',
        'customer_id',
        'disabled_at'
    ];

    protected $dates = [
        'disabled_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function printJobs()
    {
        return $this->hasMany(PrintJob::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function getHostnameAndNameAttribute()
    {
        return $this->name . ' (' . $this->hostname . ')';
    }
}
