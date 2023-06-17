<?php

namespace App\Models;

use App\Traits\HasBarcodeTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\{Arr, Carbon};
use Webpatser\Countries\Countries;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Events\AuditCustom;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property int $customer_id
 * @property string $sku
 * @property string $name
 * @property string $price
 * @property string|null $notes
 * @property int $quantity_on_hand
 * @property int $quantity_pickable
 * @property int $quantity_allocated
 * @property int $quantity_allocated_pickable
 * @property int $quantity_available
 * @property int $quantity_to_replenish
 * @property string|null $value
 * @property string|null $customs_price
 * @property string|null $customs_description
 * @property string|null $hs_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $quantity_backordered
 * @property float|null $weight
 * @property float|null $height
 * @property float|null $length
 * @property float|null $width
 * @property string|null $barcode
 * @property int|null $country_of_origin
 * @property int $is_kit
 * @property string|null $priority_counting_requested_at
 * @property int|null $reorder_threshold
 * @property int|null $quantity_reorder
 * @property string|null $last_counted_at
 * @property int $has_serial_number
 * @property string $kit_type
 * @property int|null $lot_tracking
 * @property int|null $lot_priority
 * @property-read Countries|null $country
 * @property-read \App\Models\Customer $customer
 * @property-read Collection|\App\Models\InventoryLog[] $inventoryLogs
 * @property-read int|null $inventory_logs_count
 * @property-read Collection|Product[] $kitItems
 * @property-read int|null $kit_items_count
 * @property-read Collection|Product[] $kitParents
 * @property-read int|null $kit_parents_count
 * @property-read Collection|\App\Models\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read Collection|\App\Models\LotItem[] $lotItems
 * @property-read int|null $lot_items_count
 * @property-read Collection|\App\Models\Lot[] $lots
 * @property-read int|null $lots_count
 * @property-read Collection|\App\Models\OrderItem[] $orderItem
 * @property-read int|null $order_item_count
 * @property-read Collection|\App\Models\Image[] $productImages
 * @property-read int|null $product_images_count
 * @property-read Collection|\App\Models\PurchaseOrderItem[] $purchaseOrderLine
 * @property-read int|null $purchase_order_line_count
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read Collection|\App\Models\ShipmentItem[] $shipmentItem
 * @property-read int|null $shipment_item_count
 * @property-read Collection|\App\Models\Supplier[] $suppliers
 * @property-read int|null $suppliers_count
 * @property-read Collection|\App\Models\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static \Database\Factories\ProductFactory factory(...$parameters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBarcode($value)
 * @method static Builder|Product whereCountryOfOrigin($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereCustomerId($value)
 * @method static Builder|Product whereCustomsDescription($value)
 * @method static Builder|Product whereCustomsPrice($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereHasSerialNumber($value)
 * @method static Builder|Product whereHeight($value)
 * @method static Builder|Product whereHsCode($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereIsKit($value)
 * @method static Builder|Product whereKitType($value)
 * @method static Builder|Product whereLastCountedAt($value)
 * @method static Builder|Product whereLength($value)
 * @method static Builder|Product whereLotPriority($value)
 * @method static Builder|Product whereLotTracking($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product whereNotes($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product wherePriorityCountingRequestedAt($value)
 * @method static Builder|Product whereQuantityAllocated($value)
 * @method static Builder|Product whereQuantityAllocatedPickable($value)
 * @method static Builder|Product whereQuantityAvailable($value)
 * @method static Builder|Product whereQuantityBackordered($value)
 * @method static Builder|Product whereQuantityOnHand($value)
 * @method static Builder|Product whereQuantityPickable($value)
 * @method static Builder|Product whereQuantityReorder($value)
 * @method static Builder|Product whereQuantityToReplenish($value)
 * @method static Builder|Product whereReorderThreshold($value)
 * @method static Builder|Product whereSku($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product whereValue($value)
 * @method static Builder|Product whereWeight($value)
 * @method static Builder|Product whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model implements AuditableInterface
{
    protected $fillable = [
        'sku',
        'name',
        'price',
        'notes',
        'customer_id',
        'quantity_on_hand',
        'quantity_pickable',
        'quantity_allocated',
        'quantity_allocated_pickable',
        'quantity_available',
        'quantity_backordered',
        'quantity_to_replenish',
        'height',
        'weight',
        'length',
        'width',
        'barcode',
        'hs_code',
        'value',
        'customs_price',
        'customs_description',
        'country_of_origin',
        'priority_counting_requested_at',
        'has_serial_number',
        'reorder_threshold',
        'quantity_reorder',
        'lot_tracking',
        'lot_priority'
    ];

    use HasFactory, RevisionableTrait, SoftDeletes, HasBarcodeTrait, AuditableTrait;

    protected $attributes = [
        'height' => 0,
        'weight' => 0,
        'length' => 0,
        'width' => 0,
    ];

    protected $revisionCreationsEnabled = true;

    protected $revisionForceDeleteEnabled = true;

    public const PRODUCT_TYPE_REGULAR = 0;
    public const PRODUCT_TYPE_STATIC_KIT = 1;
    public const PRODUCT_TYPE_DYNAMIC_KIT = 2;

    protected $auditEvents = [
        'created' => 'getCreatedEventAttributes',
        'updated' => 'getUpdatedEventAttributes',
    ];

    protected $auditInclude = [
        'sku',
        'name',
        'price',
        'notes',
        'quantity_on_hand',
        'quantity_pickable',
        'quantity_allocated',
        'quantity_allocated_pickable',
        'quantity_available',
        'quantity_to_replenish',
        'value',
        'customs_price',
        'customs_description',
        'hs_code',
        'quantity_backordered',
        'weight',
        'height',
        'length',
        'width',
        'barcode',
        'country_of_origin',
        'priority_counting_requested_at',
        'has_serial_number',
        'kit_type',
        'reorder_threshold',
        'quantity_reorder',
        'last_counted_at',
        'lot_tracking',
        'lot_priority',
    ];

    public function purchaseOrderLine()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function shipmentItem()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function orderItem()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class)
            ->using(LocationProduct::class)
            ->withPivot([
                'quantity_on_hand',
                'quantity_reserved_for_picking'
            ])
            ->orderBy('name');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function productImages()
    {
        return $this->morphMany(Image::class, 'object');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'object')->withTrashed();
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function kitItems()
    {
        return $this->belongsToMany(__CLASS__, 'kit_items', 'parent_product_id', 'child_product_id')->withPivot(['quantity']);
    }

    public function kitParents()
    {
        return $this->belongsToMany(__CLASS__, 'kit_items', 'child_product_id', 'parent_product_id')->withPivot(['quantity']);
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne( Countries::class, 'id', 'country_of_origin');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }

    public function lotItems()
    {
        return $this->hasManyThrough(LotItem::class, Lot::class);
    }

    /**
     * @param $value
     */
    public function setReorderThresholdAttribute($value)
    {
        $this->attributes['reorder_threshold'] = (int)$value;
    }

    /**
     * @param $value
     */
    public function setQuantityReorderAttribute($value)
    {
        $this->attributes['quantity_reorder'] = (int)$value;
    }

      /**
     * Get the old/new attributes of a created event.
     *
     * @return array
     */
    public function getCreatedEventAttributes(): array
    {
        $new = [];

        foreach ($this->attributes as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $new[$attribute] = $value;
            }
        }

        return [[], $new];
    }

    /**
     * Get the old/new attributes of an updated event.
     *
     * @return array
     */
    protected function getUpdatedEventAttributes(): array
    {
        $old = [];
        $new = [];

        foreach ($this->getDirty() as $attribute => $value) {
            if ($this->isAttributeAuditable($attribute)) {
                $old[$attribute] = Arr::get($this->original, $attribute);
                $new[$attribute] = Arr::get($this->attributes, $attribute);
            }
        }

        return [$old, $new];
    }

    public static $columnTitle = [
        'priority_counting_requested_at' => 'Priority counting',
        'has_serial_number' => 'Needs serial number',
        'lot_tracking' => 'Needs Lot tracking',
    ];


    public static $objectTitle = [
        Product::class => 'Product',
        Image::class => 'Image',
    ];

    public static $columnBoolean = [
        'priority_counting_requested_at',
        'has_serial_number',
        'lot_tracking',
    ];

    public static $kitType = [
        self::PRODUCT_TYPE_REGULAR =>  'Regular',
        self::PRODUCT_TYPE_STATIC_KIT =>  'Static',
        self::PRODUCT_TYPE_DYNAMIC_KIT =>  'Dynamic',
    ];

    public static $lotPriority = [
        0 =>  'System default',
        Lot::FEFO_ID =>  'FEFO',
        Lot::FIFO_ID =>  'FIFO',
    ];

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['custom_message'] = '';

        $data = $this->setAuditMessageForOption($data);

        if ($this->auditEvent == 'created') {
            $data['custom_message'] = __('Product created from manual');
            $data['old_values'] = null;
            $data['new_values'] = ['message' => $data['custom_message']];
        } elseif ($this->auditEvent == 'updated') {
            foreach (Arr::get($data, 'new_values') as $attribute => $value) {
                if (in_array($attribute, self::$columnBoolean)) {
                    if ($attribute == 'priority_counting_requested_at' && !empty($this->getAttribute($attribute)) && !empty($this->getOriginal($attribute))) {
                        $data['old_values']['priority_counting_requested_at'] = null;
                        $data['new_values']['priority_counting_requested_at'] = null;
                    } else {
                        $new = $this->getAttribute($attribute);
                        $new = ($new == true || $new == 1) ? true : false;
                        $to = $new ? __('YES') : __('NO');
                        $from = $new ? __('NO') : __('YES');

                        $data['custom_message'] .= __(':attribute changed from :from to :to <br/>', [
                            'attribute' => Arr::get(self::$columnTitle, $attribute, str_replace('_', ' ', ucfirst($attribute))),
                            'from' => $from,
                            'to' => $to,
                        ]);
                    }
                } elseif ($attribute == 'tags') {
                    $oldTag = Arr::pluck(Arr::get($data, 'old_values.tags'), 'name');
                    $newTag = Arr::pluck(Arr::get($data, 'new_values.tags'), 'name');

                    $addedTag = array_values(array_diff($newTag, $oldTag));
                    $removedTag = array_values(array_diff($oldTag, $newTag));

                    if (!empty($removedTag)) {
                        $data['custom_message'] = __('Removed <em>":tag"</em> :attribute', ['tag' => implode(', ', $removedTag), 'attribute' => count($removedTag) > 1 ? 'tags' : 'tag']);
                    }

                    if (!empty($addedTag)) {
                        $data['custom_message'] = __('Added <em>":tag"</em> :attribute', ['tag' => implode(', ', $addedTag), 'attribute' => count($addedTag) > 1 ? 'tags' : 'tag']);
                    }
                } else {
                    $data['custom_message'] .=  $this->setAuditMessage($data, $attribute) . ' <br/>';
                }
            }
        } elseif (in_array($this->auditEvent, ['kit added', 'kit removed', 'kit updated'])) {
            $data['custom_message'] = Arr::get($data, 'new_values.message', '');
        }

        return $data;
    }

    public function setAuditMessageForOption($data) {
        if (Arr::has($data, 'new_values.kit_type')) {
            $data['old_values']['kit_type'] = Arr::get(self::$kitType, $this->getOriginal('kit_type'), '');
            $data['new_values']['kit_type'] = Arr::get(self::$kitType, $this->getAttribute('kit_type'), '');
        }

        if (Arr::has($data, 'new_values.lot_priority')) {
            $data['old_values']['lot_priority'] = $this->getOriginal('lot_priority') == null ? null: Arr::get(self::$lotPriority, $this->getOriginal('lot_priority'), null);
            $data['new_values']['lot_priority'] = Arr::get(self::$lotPriority, $this->getAttribute('lot_priority'), '');
        }

        if (Arr::has($data, 'new_values.country_of_origin')) {
            $data['old_values']['country_of_origin'] = Countries::find($this->getOriginal('country_of_origin'))->name ?? '';
            $data['new_values']['country_of_origin'] = Countries::find($this->getAttribute('country_of_origin'))->name ?? '';
        }

        return $data;
    }

    public function setAuditMessage($data, $attribute)
    {
        $field = Arr::get(self::$columnTitle, $attribute, str_replace('_', ' ', ucfirst($attribute)));
        $newValue = Arr::get($data, 'new_values.' . $attribute);
        $oldValue = Arr::get($data, 'old_values.' . $attribute);

        if (empty($newValue)) {
            return __(':old was removed from :field', ['old' => $this->getAuditValue($oldValue), 'field' => $field]);
        } else if (empty($oldValue)) {
            return __(':field set to :new', ['field' => $field, 'new' => $this->getAuditValue($newValue)]);
        } else {
            return __(':field changed from :old to :new', ['field' => $field, 'old' => $this->getAuditValue($oldValue), 'new' => $this->getAuditValue($newValue)]);
        }
    }

    public function getAuditValue($str) {
        return '<em>"' . $str . '"</em>';
    }

    public function auditProductCustomEvent($event, $message)
    {
        $this->auditEvent = $event;
        $this->isCustomEvent = true;
        $this->auditCustomOld = [];
        $this->auditCustomNew = ['message' => $message];

        Event::dispatch(AuditCustom::class, [$this]);

        $this->isCustomEvent = false;

        return $this;
    }

    public function auditKitItems($oldKitItems, $newKitItems)
    {
        $oldData = [];
        $oldIds = [];

        foreach ($oldKitItems as $kitItem) {
            $oldData[] = ['product_id' => $kitItem->pivot->child_product_id, 'quantity' => $kitItem->pivot->quantity, 'sku' => $kitItem->sku];
            $oldIds[] = $kitItem->id;
        }

        $newData = [];
        $newIds = [];

        foreach ($newKitItems as $kitItem) {
            $newData[] = ['product_id' => $kitItem->pivot->child_product_id, 'quantity' => $kitItem->pivot->quantity, 'sku' => $kitItem->sku];
            $newIds[] = $kitItem->id;
        }

        $newAddedProductIds = array_values(array_diff($newIds, $oldIds));

        if (!empty($newAddedProductIds)) {
            $newAddedProducts = collect($newData)->whereIn('product_id', $newAddedProductIds);

            foreach ($newAddedProducts as $newAddedProduct) {
                $message = __(':quantity x :sku added to KIT', ['quantity' => $newAddedProduct['quantity'], 'sku' => $newAddedProduct['sku']]);

                $this->auditProductCustomEvent('kit added', $message);
            }
        }

        $removedProductIds = array_values(array_diff($oldIds, $newIds));

        if (!empty($removedProductIds)) {
            $removedProducts = collect($oldData)->whereIn('product_id', $removedProductIds);

            foreach ($removedProducts as $removedProduct) {
                $message = __(':quantity x :sku removed from KIT', ['quantity' => $removedProduct['quantity'], 'sku' => $removedProduct['sku']]);

                $this->auditProductCustomEvent('kit removed', $message);
            }
        }

        foreach ($newKitItems as $newKitItem) {
            foreach ($oldKitItems as $oldKitItem) {
                if ($newKitItem->id == $oldKitItem->id && $newKitItem->pivot->quantity != $oldKitItem->pivot->quantity) {
                    $message = __('Quantity changed from <em>":old"</em> to <em>":new"</em> for :sku', ['old' => $oldKitItem->pivot->quantity, 'new' => $newKitItem->pivot->quantity, 'sku' => $newKitItem->sku]);

                    $this->auditProductCustomEvent('kit updated', $message);
                }
            }
        }
    }

    public function getAllAudits()
    {
        $product = $this->load('audits.user.contactInformation', 'images.audits.user.contactInformation');
        $audits = $product->audits;

        $product->images->map(function($image, $key) use($audits) {
            $image->audits->map(function($audit, $key) use($audits) {
                $audits->push($audit);
            });
        });

        return $this->prepareEachAudits($audits->sortByDesc('created_at'));
    }

    protected function prepareEachAudits($audits)
    {
        $audits->map(function($audit, $key) {
            $audit->object_name = Arr::get(self::$objectTitle, $audit->auditable_type, str_replace("App\Models\\", "", $audit->auditable_type));
        });

        return $audits;
    }

    /**
     * @return bool
     */
    public function isKit(): bool
    {
        return !($this->kit_type == self::PRODUCT_TYPE_REGULAR);
    }

    /**
     * @return bool
     */
    public function isKitItem(): bool
    {
        return DB::table('kit_items')
            ->where('child_product_id', $this->id)
            ->count() > 0;
    }
}
