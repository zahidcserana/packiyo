<?php

namespace App\Models;

use App\Traits\HasUniqueIdentifierSuggestionTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Return_
 *
 * @property-read Collection|InventoryLog[] $inventoryLogSources
 * @property-read int|null $inventory_log_sources_count
 * @property-read Order $order
 * @method static bool|null forceDelete()
 * @method static Builder|Return_ newModelQuery()
 * @method static Builder|Return_ newQuery()
 * @method static \Illuminate\Database\Query\Builder|Return_ onlyTrashed()
 * @method static Builder|Return_ query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|Return_ withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Return_ withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $order_id
 * @property string $number
 * @property Carbon|null $requested_at
 * @property Carbon|null $expected_at
 * @property Carbon|null $received_at
 * @property string $reason
 * @property int $approved
 * @property string $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|ReturnItem[] $returnItems
 * @property-read int|null $return_items_count
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @method static Builder|Return_ whereApproved($value)
 * @method static Builder|Return_ whereCreatedAt($value)
 * @method static Builder|Return_ whereDeletedAt($value)
 * @method static Builder|Return_ whereExpectedAt($value)
 * @method static Builder|Return_ whereId($value)
 * @method static Builder|Return_ whereNotes($value)
 * @method static Builder|Return_ whereNumber($value)
 * @method static Builder|Return_ whereOrderId($value)
 * @method static Builder|Return_ whereReason($value)
 * @method static Builder|Return_ whereReceivedAt($value)
 * @method static Builder|Return_ whereRequestedAt($value)
 * @method static Builder|Return_ whereUpdatedAt($value)
 * @property int $warehouse_id
 * @property int|null $return_status_id
 * @property float|null $weight
 * @property float|null $height
 * @property float|null $length
 * @property float|null $width
 * @property-read Collection|ReturnItem[] $items
 * @property-read int|null $items_count
 * @property-read Collection|ReturnLabel[] $returnLabels
 * @property-read int|null $return_labels_count
 * @property-read ReturnStatus|null $returnStatus
 * @property-read Collection|ReturnTracking[] $returnTrackings
 * @property-read int|null $return_trackings_count
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Warehouse|null $warehouse
 * @method static Builder|Return_ whereHeight($value)
 * @method static Builder|Return_ whereLength($value)
 * @method static Builder|Return_ whereReturnStatusId($value)
 * @method static Builder|Return_ whereWarehouseId($value)
 * @method static Builder|Return_ whereWeight($value)
 * @method static Builder|Return_ whereWidth($value)
 */
class Return_ extends Model
{
    use SoftDeletes, CascadeSoftDeletes, RevisionableTrait, HasUniqueIdentifierSuggestionTrait;

    public static $uniqueIdentifierColumn = 'number';
    public static $uniqueIdentifierReferenceColumn = 'warehouse_id';
    public const STATUS_PENDING = 'Pending';

    protected $table = 'returns';

    protected $cascadeDeletes = [
        'returnItems'
    ];

    protected $fillable = [
        'order_id',
        'warehouse_id',
        'return_status_id',
        'number',
        'approved',
        'reason',
        'notes',
        'weight',
        'height',
        'length',
        'width',
    ];

    protected $dates = [
        'requested_at',
        'expected_at',
        'received_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id', 'id');
    }

    public function returnStatus()
    {
        return $this->belongsTo(ReturnStatus::class);
    }

    public function returnItems()
    {
        return $this->belongsToMany(Product::class, 'return_items', 'return_id', 'product_id');
    }

    public function inventoryLogSources()
    {
        return $this->morphMany(InventoryLog::class, 'source');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function returnLabels()
    {
        return $this->hasMany(ReturnLabel::class, 'return_id', 'id');
    }

    public function returnTrackings()
    {
        return $this->hasMany(ReturnTracking::class, 'return_id', 'id');
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        return $this->returnStatus->name ?? self::STATUS_PENDING;
    }
}
