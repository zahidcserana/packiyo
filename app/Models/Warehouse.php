<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Warehouse
 *
 * @property int $id
 * @property int $customer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ContactInformation $contactInformation
 * @property-read Customer $customer
 * @property-read Collection|Location[] $locations
 * @property-read int|null $locations_count
 * @method static bool|null forceDelete()
 * @method static Builder|Warehouse newModelQuery()
 * @method static Builder|Warehouse newQuery()
 * @method static \Illuminate\Database\Query\Builder|Warehouse onlyTrashed()
 * @method static Builder|Warehouse query()
 * @method static bool|null restore()
 * @method static Builder|Warehouse whereContactInformationId($value)
 * @method static Builder|Warehouse whereCreatedAt($value)
 * @method static Builder|Warehouse whereCustomerId($value)
 * @method static Builder|Warehouse whereDeletedAt($value)
 * @method static Builder|Warehouse whereId($value)
 * @method static Builder|Warehouse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Warehouse withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Warehouse withoutTrashed()
 * @mixin \Eloquent
 */
class Warehouse extends Model
{
    use SoftDeletes, CascadeSoftDeletes, HasFactory;

    protected $cascadeDeletes = [
        'locations',
        'contactInformation'
    ];

    protected $fillable = [
        'customer_id'
    ];

    public function contactInformation()
    {
        return $this->morphOne(ContactInformation::class, 'object')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function getInformationAttribute()
    {
        return implode(', ', [
            $this->contactInformation->name,
            $this->contactInformation->email,
            $this->contactInformation->zip,
            $this->contactInformation->city,
        ]);
    }

    public function reshipLocation()
    {
        return Location::where('name', Location::PROTECTED_LOC_NAME_RESHIP)->where('warehouse_id', $this->id)->first();
    }
}
