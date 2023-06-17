<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\{Eloquent\Builder,
    Eloquent\Collection,
    Eloquent\Factories\HasFactory,
    Eloquent\Model,
    Eloquent\SoftDeletes};
use Illuminate\Support\{Arr, Carbon};
use \Venturecraft\Revisionable\RevisionableTrait;
use Igaster\LaravelCities\Geo;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Events\AuditCustom;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $order_channel_id
 * @property int|null $shipping_contact_information_id
 * @property int|null $billing_contact_information_id
 * @property int|null $order_status_id
 * @property int|null $shipping_method_id
 * @property string $number
 * @property Carbon|null $ordered_at
 * @property Carbon|null $fulfilled_at
 * @property Carbon|null $required_shipping_date_at
 * @property Carbon|null $shipping_date_before_at
 * @property string|null $slip_note
 * @property string|null $internal_note
 * @property string|null $packing_note
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $gift_note
 * @property int $fraud_hold
 * @property int $address_hold
 * @property int $payment_hold
 * @property int $operator_hold
 * @property int|null $priority_score
 * @property int $allow_partial
 * @property int $ready_to_ship
 * @property float|null $subtotal
 * @property float|null $shipping
 * @property float|null $tax
 * @property float|null $total
 * @property int|null $currency_id
 * @property string|null $ship_by_date
 * @property int|null $drop_point_id
 * @property string|null $shipping_lat
 * @property string|null $shipping_lng
 * @property Carbon|null $cancelled_at
 * @property int|null $external_id
 * @property string|null $shipping_method_name
 * @property string|null $order_slip
 * @property float $weight
 * @property string|null $shipping_method_code
 * @property int $quantity_pending_sum
 * @property int $quantity_allocated_sum
 * @property int $quantity_allocated_pickable_sum
 * @property int $ready_to_pick
 * @property int $allocation_hold
 * @property string|null $custom_invoice_url
 * @property float|null $discount
 * @property float $pending_weight
 * @property-read ContactInformation|null $billingContactInformation
 * @property-read Collection|BulkShipBatch[] $bulkShipBatch
 * @property-read int|null $bulk_ship_batch_count
 * @property-read Currency|null $currency
 * @property-read Customer $customer
 * @property-read mixed $age
 * @property-read bool $has_holds
 * @property-read mixed $multiplied_priority
 * @property-read mixed $true_priority
 * @property-read Collection|InventoryLog[] $inventoryLogDestinations
 * @property-read int|null $inventory_log_destinations_count
 * @property-read ShippingMethodMapping|null $mappedShippingMethod
 * @property-read OrderChannel|null $orderChannel
 * @property-read Collection|OrderItem[] $orderItems
 * @property-read int|null $order_items_count
 * @property-read OrderLock|null $orderLock
 * @property-read OrderStatus|null $orderStatus
 * @property-read Collection|ReturnItem[] $returnItems
 * @property-read int|null $return_items_count
 * @property-read Collection|Return_[] $returns
 * @property-read int|null $returns_count
 * @property-read Collection|\Venturecraft\Revisionable\Revision[] $revisionHistory
 * @property-read int|null $revision_history_count
 * @property-read Collection|Shipment[] $shipments
 * @property-read int|null $shipments_count
 * @property-read ContactInformation|null $shippingContactInformation
 * @property-read ShippingMethod|null $shippingMethod
 * @property-read ShippingMethodMapping|null $shippingMethodMapping
 * @property-read Collection|Tag[] $tags
 * @property-read int|null $tags_count
 * @property-read Tote|null $tote
 * @method static OrderFactory factory(...$parameters)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order notPickedOrders()
 * @method static \Illuminate\Database\Query\Builder|Order onlyTrashed()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddressHold($value)
 * @method static Builder|Order whereAllocationHold($value)
 * @method static Builder|Order whereAllowPartial($value)
 * @method static Builder|Order whereBillingContactInformationId($value)
 * @method static Builder|Order whereCancelledAt($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCurrencyId($value)
 * @method static Builder|Order whereCustomInvoiceUrl($value)
 * @method static Builder|Order whereCustomerId($value)
 * @method static Builder|Order whereDeletedAt($value)
 * @method static Builder|Order whereDiscount($value)
 * @method static Builder|Order whereDropPointId($value)
 * @method static Builder|Order whereExternalId($value)
 * @method static Builder|Order whereFraudHold($value)
 * @method static Builder|Order whereFulfilledAt($value)
 * @method static Builder|Order whereGiftNote($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereInternalNote($value)
 * @method static Builder|Order whereNumber($value)
 * @method static Builder|Order whereOperatorHold($value)
 * @method static Builder|Order whereOrderChannelId($value)
 * @method static Builder|Order whereOrderSlip($value)
 * @method static Builder|Order whereOrderStatusId($value)
 * @method static Builder|Order whereOrderedAt($value)
 * @method static Builder|Order wherePackingNote($value)
 * @method static Builder|Order wherePaymentHold($value)
 * @method static Builder|Order wherePendingWeight($value)
 * @method static Builder|Order wherePriority($value)
 * @method static Builder|Order whereQuantityAllocatedPickableSum($value)
 * @method static Builder|Order whereQuantityAllocatedSum($value)
 * @method static Builder|Order whereQuantityPendingSum($value)
 * @method static Builder|Order whereReadyToPick($value)
 * @method static Builder|Order whereReadyToShip($value)
 * @method static Builder|Order whereRequiredShippingDateAt($value)
 * @method static Builder|Order whereShipByDate($value)
 * @method static Builder|Order whereShipping($value)
 * @method static Builder|Order whereShippingContactInformationId($value)
 * @method static Builder|Order whereShippingDateBeforeAt($value)
 * @method static Builder|Order whereShippingLat($value)
 * @method static Builder|Order whereShippingLng($value)
 * @method static Builder|Order whereShippingMethodCode($value)
 * @method static Builder|Order whereShippingMethodId($value)
 * @method static Builder|Order whereShippingMethodName($value)
 * @method static Builder|Order whereShippingPriorityScore($value)
 * @method static Builder|Order whereSlipNote($value)
 * @method static Builder|Order whereSubtotal($value)
 * @method static Builder|Order whereTax($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|Order withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Order withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $return_shipping_method_id
 * @method static Builder|Order whereReturnShippingMethodId($value)
 */
class Order extends Model implements AuditableInterface
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, RevisionableTrait, AuditableTrait;

    protected $cascadeDeletes = [
        'orderItems',
        'shipments',
        'shippingContactInformation',
        'billingContactInformation',
        'returns'
    ];

    protected $fillable = [
        'customer_id',
        'order_channel_id',
        'external_id',
        'shipping_contact_information_id',
        'billing_contact_information_id',
        'order_status_id',
        'number',
        'ordered_at',
        'required_shipping_date_at',
        'shipping_date_before_at',
        'slip_note',
        'packing_note',
        'internal_note',
        'priority',
        'priority_score',
        'gift_note',
        'fraud_hold',
        'allocation_hold',
        'address_hold',
        'payment_hold',
        'operator_hold',
        'allow_partial',
        'ready_to_ship',
        'ready_to_pick',
        'tax',
        'discount',
        'shipping',
        'shipping_method_id',
        'return_shipping_method_id',
        'shipping_method_name',
        'shipping_method_code',
        'order_slip',
        'drop_point_id',
        'currency_id',
        'quantity_pending_sum',
        'quantity_allocated_sum',
        'quantity_allocated_pickable_sum',
        'custom_invoice_url'
    ];

    protected $dates = [
        'ordered_at',
        'fulfilled_at',
        'cancelled_at',
        'required_shipping_date_at',
        'shipping_date_before_at'
    ];

    public const STATUS_FULFILLED = 'Fulfilled';
    public const STATUS_CANCELLED = 'Cancelled';
    public const STATUS_PENDING = 'Pending';

    public const ORDER_STATUSES = [
        'pending' => self::STATUS_PENDING,
        'fulfilled' => self::STATUS_FULFILLED,
        'cancelled' => self::STATUS_CANCELLED
    ];

    protected $revisionCreationsEnabled = true;

    /**
     * Audit configs
     */
    protected $auditStrict = true;

    protected $auditEvents = [
        'created' => 'getCreatedEventAttributes',
        'updated' => 'getUpdatedEventAttributes',
    ];

    protected $auditInclude = [
        'required_shipping_date_at',
        'shipping_date_before_at',
        'notes',
        'gift_note',
        'internal_note',
        'packing_note',
        'slip_note',
        'weight',
        'shipping_priority_score',
        'priority',
        'order_status_id',
        'shipping_method_id',
        'fulfilled_at',
        'operator_hold',
        'payment_hold',
        'allocation_hold',
        'address_hold',
        'fraud_hold',
        'allow_partial',
        'subtotal',
        'shipping',
        'tax',
        'total',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class)->with('contactInformation')->withTrashed();
    }

    public function orderChannel()
    {
        return $this->belongsTo(OrderChannel::class);
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class)->withTrashed();
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class)->withTrashed();
    }

    public function returnShippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'return_shipping_method_id', 'id')->withTrashed();
    }

    public function shippingMethodMapping()
    {
        return $this->belongsTo(ShippingMethodMapping::class, 'shipping_method_name', 'shipping_method_name');
    }

    public function mappedShippingMethod()
    {
        return $this->belongsTo(ShippingMethodMapping::class, 'shipping_method_name', 'shipping_method_name')->where('customer_id', $this->customer_id);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItemsGreaterThanZero()
    {
        return $this->orderItems->whereNull('cancelled_at')->where('quantity', '>', 0);
    }

    public function returnItems()
    {
        return $this->hasManyThrough(
            ReturnItem::class,
            Return_::class,
            'order_id',
            'return_id',
            'id',
            'id'
        );
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function shippingContactInformation()
    {
        return $this->belongsTo(ContactInformation::class, 'shipping_contact_information_id', 'id')->withTrashed();
    }

    public function billingContactInformation()
    {
        return $this->belongsTo(ContactInformation::class, 'billing_contact_information_id', 'id')->withTrashed();
    }

    public function returns()
    {
        return $this->hasMany(Return_::class);
    }

    public function inventoryLogDestinations()
    {
        return $this->morphMany(InventoryLog::class, 'destination')->withTrashed();
    }

    public function orderLock()
    {
        return $this->hasOne(OrderLock::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function bulkShipBatch()
    {
        return $this->belongsToMany(BulkShipBatch::class)->withPivot('shipped', 'errors', 'shipment_id');
    }

    public function getMultipliedPriorityAttribute()
    {
        return $this->priority * 10;
    }

    public function getAgeAttribute()
    {
        if (!empty($this->created_at) && !empty($this->ordered_at)) {
            return $this->ordered_at->diffInDays($this->created_at);
        }

        return 0;
    }

    public function getTruePriorityAttribute()
    {
        return $this->MultipliedPriority + $this->Age;
    }

    public function unshippedOrders()
    {
        return $this->doesntHave('shipments')->get();
    }

    public function recalculateWeight()
    {
        $this->weight = 0;
        $this->pending_weight = 0;

        foreach ($this->orderItems as $orderItem) {
            $this->weight += $orderItem->weight * $orderItem->quantity;
            $this->pending_weight += $orderItem->weight * $orderItem->quantity_pending;
        }
    }

    // TODO: we should store this on the order table and recalculate when order was updated
    public function getTotal()
    {
        $total = 0;

        foreach ($this->orderItems as $orderItem) {
            $total += $orderItem->product->price * $orderItem->quantity;
        }

        return $total;
    }

    public function tote()
    {
        return $this->hasOne(Tote::class)->withTrashed();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function getMapCoordinates()
    {
        $city = Arr::get($this->shippingContactInformation, 'city', '');
        $countryCode = Arr::get($this->shippingContactInformation, 'country.iso_3166_2', '');

        $coordinates = Geo::where('name', $city)
            ->where('country', $countryCode)
            ->first();

        if ($coordinates) {
            $this->shipping_lat = $coordinates->lat;
            $this->shipping_lng = $coordinates->long;

            $this->save();
        }
    }

    public function scopeNotPickedOrders($query)
    {
        $user = auth()->user();
        $notCompletedTaskIds = Task::get()->where('user_id', $user->id)->where('taskable_type', PickingBatch::class)->where('completed_at', null)->pluck('taskable_id')->toArray();

        $orderLockIds = OrderLock::get()->where('user_id', '=', $user->id)->pluck('id')->toArray();
        $orders = Order::with('orderItems.pickingBatchItems.pickingBatch')->where('ready_to_pick', 1)->whereDoesntHave('orderLock')->orWhereHas('orderLock', function ($query) use ($orderLockIds) {
            $query->whereIn('id', $orderLockIds);
        })->get();
        $ordersIds = [];

        foreach ($orders as $order) {
            $orderItems = $order->orderItems;
            $pickingBatch = null;
            $quantity = 0;

            foreach ($orderItems as $orderItem) {
                $pickingBatchItems = $orderItem->pickingBatchItems;

                foreach ($pickingBatchItems as $pickingBatchItem) {
                    $pickingBatch = $pickingBatchItem->pickingBatch;
                    $quantity += $pickingBatchItem->quantity - $pickingBatchItem->quantity_picked;
                }
            }

            if ($pickingBatch) {
                if ($pickingBatch->type === 'so' && in_array($pickingBatch->id, $notCompletedTaskIds) && $quantity) {
                    $ordersIds[] = $order->id;
                }
            } else {
                if ($orderItems->sum('quantity_allocated')) {
                    $ordersIds[] = $order->id;
                }
            }
        }

        return $query->whereIntegerInRaw('id', $ordersIds);
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        if ($this->fulfilled_at) {
            return self::STATUS_FULFILLED;
        }

        if ($this->cancelled_at) {
            return self::STATUS_CANCELLED;
        }

        return $this->orderStatus->name ?? self::STATUS_PENDING;
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

        return [
            [],
            $new,
        ];
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

        return [
            $old,
            $new,
        ];
    }

    public static $columnTitle = [
        'shipping_date_before_at' => 'Hold order date',
        'required_shipping_date_at' => 'Required shipping date'
    ];

    public static $eventMessage = [
        'cancelled' => 'Order was cancelled',
        'fulfilled' => 'Order was fulfilled',
        'return' => 'Return was created for the order - <em>":return_number"</em>',
        'reshipped' => 'Order reshipped'
    ];

    public static $objectTitle = [
        Order::class => 'Order',
        OrderItem::class => 'Product',
        ContactInformation::class => 'Address Info',
    ];

    public static $columnBoolean = [
        'priority',
        'operator_hold',
        'payment_hold',
        'address_hold',
        'fraud_hold',
        'allocation_hold',
        'allow_partial'
    ];

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['custom_message'] = '';

        if (Arr::has($data, 'new_values.order_status_id')) {
            $data['old_values']['order_status'] = OrderStatus::find($this->getOriginal('order_status_id'))->name ?? '';
            $data['new_values']['order_status'] = OrderStatus::find($this->getAttribute('order_status_id'))->name ?? __('Pending');

            Arr::forget($data, 'old_values.order_status_id');
            Arr::forget($data, 'new_values.order_status_id');
        }

        if (Arr::has($data, 'new_values.shipping_method_id')) {
            $data['old_values']['shipping_method'] = ShippingMethod::find($this->getOriginal('shipping_method_id'))->carrierNameAndName ?? '';
            $data['new_values']['shipping_method'] = ShippingMethod::find($this->getAttribute('shipping_method_id'))->carrierNameAndName ?? '';

            Arr::forget($data, 'old_values.shipping_method_id');
            Arr::forget($data, 'new_values.shipping_method_id');
        }

        if ($this->auditEvent == 'created') {
            $data['custom_message'] = __('Order was placed');
            $data['old_values'] = null;
            $data['new_values'] = ['message' => $data['custom_message']];
        } elseif ($this->auditEvent == 'updated') {
            foreach (Arr::get($data, 'new_values') as $attribute => $value) {
                if ($attribute == 'message') {
                    $data['custom_message'] = Arr::get($data, 'new_values.message', '');
                } elseif (in_array($attribute, self::$columnBoolean)) {
                    if ($attribute == 'allow_partial') {
                        $trigger = $this->getAttribute($attribute) ? __('Enabled') : __('Disabled');
                    } else {
                        $trigger = $this->getAttribute($attribute) ? __('Added') : __('Removed');
                    }

                    $data['custom_message'] .= __(':trigger :attribute <br/>', [
                        'attribute' => str_replace('_', ' ', $attribute),
                        'trigger' => $trigger,
                    ]);
                } elseif ($attribute == 'shipping_priority_score') {
                    $data['custom_message'] .= __('Priority updated to <em>":new"</em> <br/>', [
                        'attribute' => str_replace('_', ' ', ucfirst($attribute)),
                        'new' => $this->getAttribute($attribute)
                    ]);
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
        } elseif (in_array($this->auditEvent, ['cancelled', 'fulfilled', 'shipped', 'return', 'reshipped'])) {
            $data['custom_message'] = Arr::get($data, 'new_values.message', '');
        }

        return $data;
    }

    public function setAuditMessage($data, $attribute) {
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

    public function getAllAudits()
    {
        $order = $this->load('audits.user.contactInformation', 'shippingContactInformation.audits.user.contactInformation', 'billingContactInformation.audits.user.contactInformation', 'orderItems.audits.user.contactInformation');

        $audits = collect([$order->shippingContactInformation->audits, $order->billingContactInformation->audits])->reduce(function($collection, $item) {
            if (empty($item) || $item->isEmpty()) {
                return $collection;
            }
            return $collection->merge($item);
        }, $order->audits);

        $order->orderItems->map(function($orderItem, $key) use($audits) {
            $orderItem->audits->map(function($audit, $key) use($audits) {
                $audits->push($audit);
            });
        });

        return $this->prepareEachAudits($audits->sortByDesc('created_at'));
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function auditOrderCustomEvent($event = null, $returnNumber = null)
    {
        $this->auditEvent = $event;
        $this->isCustomEvent = true;
        $this->auditCustomOld = [];
        $this->auditCustomNew = [
            'message' => __(Arr::get(self::$eventMessage, $event), ['return_number' => $returnNumber])
        ];

        Event::dispatch(AuditCustom::class, [$this]);

        $this->isCustomEvent = false;

        return $this;
    }

    public function auditTagCustomEvent($tags)
    {
        $this->auditEvent = 'updated';
        $this->isCustomEvent = true;
        $this->auditCustomOld = [];
        $this->auditCustomNew = [
            'message' => __('Removed <em>":tag"</em> :attribute', ['tag' => implode(', ', $tags), 'attribute' => count($tags) > 1 ? 'tags' : 'tag'])
        ];

        Event::dispatch(AuditCustom::class, [$this]);

        $this->isCustomEvent = false;

        return $this;
    }

    /**
     * @param Shipment $shipment
     * @return Order
     */
    public function auditSingleOrderShipCustomEvent(Shipment $shipment)
    {
        $this->auditEvent = 'shipped';
        $this->isCustomEvent = true;
        $shipmentTrackings = '';

        if (!is_null($shipment->shipmentTrackings)) {
            foreach($shipment->shipmentTrackings as $tracking){
                $shipmentTrackings .= $tracking->tracking_url . ', ' . $tracking->tracking_number;
            }
        }

        $this->auditCustomOld = [];
        $this->auditCustomNew = [
            'message' => __('Order was shipped using :shippingMethod :shipmentTrackings', [
                'shippingMethod' => !is_null($shipment->shippingMethod) ? $shipment->shippingMethod->carrierNameAndName : 'Dummy',
                'shipmentTrackings' => $shipmentTrackings != '' ? ' - ' . $shipmentTrackings : ''
            ])
        ];

        Event::dispatch(AuditCustom::class, [$this]);

        $this->isCustomEvent = false;

        return $this;
    }

    protected function prepareEachAudits($audits)
    {
        $audits->map(function($audit, $key) {
            $audit->object_name = Arr::get(self::$objectTitle, $audit->auditable_type, str_replace("App\Models\\", "", $audit->auditable_type));
        });

        return $audits;
    }

    /**
     * @return boolean
     */
    public function isEmptyOrderItemQuantityShipped(): bool
    {
        return $this
            ->orderItems
            ->filter(function ($item) {
                return $item->quantity_shipped > 0;
            })
            ->isEmpty();
    }

    public function getHasHoldsAttribute(): bool
    {
        return $this->address_hold || $this->fraud_hold || $this->payment_hold || $this->operator_hold;
    }

    /**
     * @return array
     */
    public function notReadyToShipExplanation(): array
    {
        $reasons = [];

        if ($this->address_hold) {
            $reasons[] = __('The order has an address hold added');
        }

        if ($this->fraud_hold) {
            $reasons[] = __('The order has a fraud hold added');
        }

        if ($this->payment_hold) {
            $reasons[] = __('The order has a payment hold added');
        }

        if ($this->operator_hold) {
            $reasons[] = __('The order has an operator hold added');
        }

        if ($this->allocation_hold) {
            $reasons[] = __('The order has an allocation hold added');
        }

        if (!is_null($this->required_shipping_date_at) && $this->required_shipping_date_at > Carbon::now()) {
            $reasons[] = __('The hold until date was not met');
        }

        if (!$this->allow_partial) {
            if ($this->quantity_allocated_sum !== $this->quantity_pending_sum) {
                $pendingItems = [];
                foreach ($this->orderItems as $orderItem) {
                    if ($orderItem->quantity_backordered > 0) {
                        $pendingItems[] = $orderItem->sku;
                    }
                }

                if (empty($pendingItems)) {
                    $reasons[] = __('Some order items could not be allocated');
                }

                $reasons[] = __('Order items with the following SKU were not allocated: ') . implode(', ', $pendingItems);
            }
        }

        return $reasons;
    }

    /**
     * @return string|null
     */
    public function notReadyToPickExplanation(): ?string
    {
        if (!$this->allow_partial) {
            if ($this->quantity_allocated_pickable_sum !== $this->quantity_pending_sum) {
                $pendingItems = [];
                foreach ($this->orderItems as $orderItem) {
                    if (!$orderItem->product->isKit() && $orderItem->quantity_allocated_pickable < $orderItem->quantity_pending) {
                        $pendingItems[] = $orderItem->sku;
                    }
                }

                if (empty($pendingItems)) {
                    return __('Some order items could not be allocated from pickable locations');
                }

                return __('Order items with the following SKU were not allocated from pickable locations: ') . implode(', ', $pendingItems);
            }
        }

        return null;
    }
}
