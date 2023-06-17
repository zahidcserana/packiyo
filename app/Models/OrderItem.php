<?php

namespace App\Models;

use Illuminate\Database\{
    Eloquent\Builder,
    Eloquent\Collection,
    Eloquent\Model,
    Eloquent\SoftDeletes};
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Support\{Arr, Carbon};
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * App\Models\OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property float $quantity
 * @property float $quantity_shipped
 * @property int $quantity_returned
 * @property float $quantity_pending
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $quantity_allocated
 * @property int $quantity_allocated_pickable
 * @property int $quantity_backordered
 * @property string $sku
 * @property string $name
 * @property string $price
 * @property float $weight
 * @property float $height
 * @property float $length
 * @property float $width
 * @property int|null $order_item_kit_id
 * @property string|null $external_id
 * @property string|null $cancelled_at
 * @property int $quantity_reshipped
 * @property string|null $ordered_at
 * @property-read mixed $first_product_image_source
 * @property-read Collection|OrderItem[] $kitOrderItems
 * @property-read int|null $kit_order_items_count
 * @property-read \App\Models\Order $order
 * @property-read Collection|\App\Models\PackageOrderItem[] $packageOrderItems
 * @property-read int|null $package_order_items_count
 * @property-read OrderItem|null $parentOrderItem
 * @property-read Collection|\App\Models\PickingBatchItem[] $pickingBatchItems
 * @property-read int|null $picking_batch_items_count
 * @property-read Collection|\App\Models\ToteOrderItem[] $placedToteOrderItems
 * @property-read int|null $placed_tote_order_items_count
 * @property-read \App\Models\Product $product
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read Collection|\App\Models\ShipmentItem[] $shipmentItems
 * @property-read int|null $shipment_items_count
 * @property-read Collection|\App\Models\ToteOrderItem[] $toteOrderItems
 * @property-read int|null $tote_order_items_count
 * @method static Builder|OrderItem newModelQuery()
 * @method static Builder|OrderItem newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderItem onlyTrashed()
 * @method static Builder|OrderItem query()
 * @method static Builder|OrderItem whereCancelledAt($value)
 * @method static Builder|OrderItem whereCreatedAt($value)
 * @method static Builder|OrderItem whereDeletedAt($value)
 * @method static Builder|OrderItem whereExternalId($value)
 * @method static Builder|OrderItem whereHeight($value)
 * @method static Builder|OrderItem whereId($value)
 * @method static Builder|OrderItem whereLength($value)
 * @method static Builder|OrderItem whereName($value)
 * @method static Builder|OrderItem whereOrderId($value)
 * @method static Builder|OrderItem whereOrderItemKitId($value)
 * @method static Builder|OrderItem whereOrderedAt($value)
 * @method static Builder|OrderItem wherePrice($value)
 * @method static Builder|OrderItem whereProductId($value)
 * @method static Builder|OrderItem whereQuantity($value)
 * @method static Builder|OrderItem whereQuantityAllocated($value)
 * @method static Builder|OrderItem whereQuantityAllocatedPickable($value)
 * @method static Builder|OrderItem whereQuantityBackordered($value)
 * @method static Builder|OrderItem whereQuantityPending($value)
 * @method static Builder|OrderItem whereQuantityReshipped($value)
 * @method static Builder|OrderItem whereQuantityReturned($value)
 * @method static Builder|OrderItem whereQuantityShipped($value)
 * @method static Builder|OrderItem whereSku($value)
 * @method static Builder|OrderItem whereUpdatedAt($value)
 * @method static Builder|OrderItem whereWeight($value)
 * @method static Builder|OrderItem whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|OrderItem withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderItem withoutTrashed()
 * @mixin \Eloquent
 */
class OrderItem extends Model implements AuditableInterface
{
    use RevisionableTrait, AuditableTrait;

    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'external_id',
        'product_id',
        'quantity',
        'quantity_shipped',
        'quantity_returned',
        'quantity_pending',
        'quantity_allocated',
        'quantity_allocated_pickable',
        'quantity_backordered',
        'sku',
        'name',
        'price',
        'height',
        'length',
        'weight',
        'width',
        'order_item_kit_id',
        'cancelled_at',
        'quantity_reshipped',
        'ordered_at'
    ];

    protected $attributes = [
        'quantity_shipped' => 0
    ];

    protected $revisionCreationsEnabled = true;

    protected $revisionForceDeleteEnabled = true;

    protected $appends = ['first_product_image_source'];

    /**
     * Audit configs
     */
    protected $auditStrict = true;

    protected $auditEvents = [
        'created' => 'getCreatedEventAttributes',
        'updated' => 'getUpdatedEventAttributes'
    ];

    protected $auditInclude = [
        'quantity',
        'quantity_shipped',
        'sku',
        'cancelled_at'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function kitOrderItems()
    {
        return $this->hasMany(__CLASS__, 'order_item_kit_id');
    }

    public function parentOrderItem()
    {
        return $this->belongsTo(__CLASS__, 'order_item_kit_id');
    }

    public function toteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class)->withTrashed();
    }

    public function placedToteOrderItems()
    {
        return $this->hasMany(ToteOrderItem::class)->whereRaw('quantity_remaining > 0')->withTrashed();
    }

    public function tote() {
        return $this->placedToteOrderItems->first()->tote ?? null;
    }

    public function pickingBatchItems()
    {
        return $this->hasMany(PickingBatchItem::class);
    }

    public function packageOrderItems()
    {
        return $this->hasMany(PackageOrderItem::class, 'order_item_id');
    }

    public function getFirstProductImageSourceAttribute()
    {
        return $this->product->productImages->first()->source ?? '';
    }

    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    /**
     * Get the old/new attributes of a created event.
     *
     * @return array
     */
    protected function getCreatedEventAttributes(): array
    {
        $new = [];

        if (in_array(AuditableTrait::class, class_uses_recursive(get_class($this->order)))) {
            foreach ($this->attributes as $attribute => $value) {
                if ($this->isAttributeAuditable($attribute)) {
                    $new[$attribute] = $value;
                }
            }
        }

        return [
            [],
            $new,
        ];
    }

    /**
     * Get the old/new attributes of a created event.
     *
     * @return array
     */
    public function getUpdatedEventAttributes(): array
    {
        $old = [];
        $new = [];

        if (in_array(AuditableTrait::class, class_uses_recursive(get_class($this->order)))) {
            foreach ($this->getDirty() as $attribute => $value) {
                if ($this->isAttributeAuditable($attribute)) {
                    $old[$attribute] = Arr::get($this->original, $attribute);
                    $new[$attribute] = Arr::get($this->attributes, $attribute);
                }
            }
        }

        return [
            $old,
            $new,
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['custom_message'] = '';

        if ($this->auditEvent == 'created') {
            if(Arr::has($data, 'new_values.sku') && empty($data['old_values'])) {
                $data['custom_message'] = __('Added :quantity x :sku', [
                    'quantity' => $this->getAttribute('quantity'),
                    'sku' => $this->getAttribute('sku'),
                ]);
            }
        } elseif ($this->auditEvent == 'updated') {
            if(Arr::hasAny($data, ['new_values.quantity', 'new_values.quantity_shipped'])) {
                foreach($data['new_values'] as $attribute => $value) {
                    $data['custom_message'] .= __(':attribute changed from "<em>:old</em>" to "<em>:new</em>" for :sku <br/>', [
                        'attribute' => str_replace('_', ' ', ucfirst($attribute)),
                        'new' => $this->getAttribute($attribute),
                        'old' => Arr::get($data, 'old_values.' . $attribute),
                        'sku' => $this->getAttribute('sku'),
                    ]);
                }
            }

            if (Arr::has($data, 'new_values.cancelled_at')) {
                $data['custom_message'] = __('Canceled :quantity x :sku', [
                    'quantity' => $this->getAttribute('quantity'),
                    'sku' => $this->getAttribute('sku'),
                ]);
            }
        }

        return $data;
    }
}
