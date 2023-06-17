<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Factories\HasFactory,
    Model,
    Relations\HasMany,
    Relations\HasOne,
    SoftDeletes};
use Illuminate\Support\Carbon;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

/**
 * App\Models\Customer
 *
 * @property int $id
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Customer[] $children
 * @property-read int|null $children_count
 * @property-read ContactInformation $contactInformation
 * @property-read Collection|OrderStatus[] $orderStatuses
 * @property-read int|null $order_statuses_count
 * @property-read Collection|Order[] $orders
 * @property-read int|null $orders_count
 * @property-read Customer $parent
 * @property-read Collection|PurchaseOrder[] $purchaseOrders
 * @property-read int|null $purchase_orders_count
 * @property-read Collection|PurchaseOrderStatus[] $purchaseOrdersStatuses
 * @property-read int|null $purchase_orders_statuses_count
 * @property-read Collection|Return_[] $returns
 * @property-read int|null $returns_count
 * @property-read Collection|Supplier[] $suppliers
 * @property-read int|null $suppliers_count
 * @property-read Collection|TaskType[] $taskTypes
 * @property-read int|null $task_types_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property-read Collection|Warehouse[] $warehouses
 * @property-read int|null $warehouses_count
 * @method static bool|null forceDelete()
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static \Illuminate\Database\Query\Builder|Customer onlyTrashed()
 * @method static Builder|Customer query()
 * @method static bool|null restore()
 * @method static Builder|Customer whereContactInformationId($value)
 * @method static Builder|Customer whereCreatedAt($value)
 * @method static Builder|Customer whereDeletedAt($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereParentId($value)
 * @method static Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Customer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Customer withoutTrashed()
 * @mixin \Eloquent
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @property-read Collection|Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read Collection|Webhook[] $webhooks
 * @property-read int|null $webhooks_count
 * @property-read WebshipperCredential[]|null $webshipperCredentials
 * @property-read EasypostCredential[]|null $EasypostCredential
 * @property string $weight_unit
 * @property string $dimensions_unit
 * @method static Builder|Customer whereDimensionsUnit($value)
 * @method static Builder|Customer whereWeightUnit($value)
 * @property string $locale
 * @property string|null $custom_css
 * @property-read Image|null $orderSlipLogo
 * @property-read Collection|ShippingBox[] $shippingBoxes
 * @property-read int|null $shipping_boxes_count
 * @property-read Collection|ShippingCarrier[] $shippingCarriers
 * @method static CustomerFactory factory(...$parameters)
 * @method static Builder|Customer whereCustomCss($value)
 * @method static Builder|Customer whereLocale($value)
 * @method static Builder|Customer whereOrderSlipHeading($value)
 * @method static Builder|Customer whereOrderSlipTextbox($value)
 * @property int|null $label_printer_id
 * @property int|null $barcode_printer_id
 * @property int|null $order_slip_printer_id
 * @property int|null $packing_slip_printer_id
 * @property int $order_slip_auto_print
 * @property string $currency
 * @property-read Printer|null $barcodePrinter
 * @property-read Printer|null $labelPrinter
 * @property-read Printer|null $orderSlipPrinter
 * @property-read Printer|null $packingSlipPrinter
 * @property-read Collection|Printer[] $printers
 * @property-read int|null $printers_count
 * @method static Builder|Customer whereAutoPrintOrderSlip($value)
 * @method static Builder|Customer whereBarcodePrinterId($value)
 * @method static Builder|Customer whereCurrency($value)
 * @method static Builder|Customer whereLabelPrinterId($value)
 * @method static Builder|Customer whereOrderSlipPrinterId($value)
 * @property-read Collection|LocationType[] $locationTypes
 * @property-read int|null $location_types_count
 * @property-read Collection|Warehouse[] $parentWarehouses
 * @property-read int|null $parent_warehouses_count
 * @property int $allow_child_customers
 * @method static Builder|Customer whereAllowChildCustomers($value)
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property string|null $trial_ends_at
 * @property-read Collection|EasypostCredential[] $easypostCredentials
 * @property-read int|null $easypost_credentials_count
 * @property-read Collection|OrderChannel[] $orderChannels
 * @property-read int|null $order_channels_count
 * @property-read int|null $shipping_carriers_count
 * @property-read Collection|ShippingMethod[] $shippingMethods
 * @property-read int|null $shipping_methods_count
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read int|null $webshipper_credentials_count
 * @method static Builder|Customer wherePmLastFour($value)
 * @method static Builder|Customer wherePmType($value)
 * @method static Builder|Customer whereStripeId($value)
 * @method static Builder|Customer whereTrialEndsAt($value)
 */
class Customer extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes, Billable;

    protected $cascadeDeletes = [
        'contactInformation',
        'orders',
        'orderStatuses',
        'warehouses',
        'purchaseOrders',
        'purchaseOrdersStatuses',
        'suppliers',
        'taskTypes',
        'products',
        'tasks',
        'printers'
    ];

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'parent_id',
    ];

    public const WEIGHT_UNITS = [
        'lb' => 'pounds',
        'oz' => 'ounces',
        'kg' => 'kilograms',
        'g' => 'grams',
        'l' => 'litres'
    ];

    public const WEIGHT_UNIT_DEFAULT = 'g';

    public const DIMENSION_UNITS = [
        'in' => 'inches',
        'cm' => 'centimetres'
    ];

    public const DIMENSION_UNIT_DEFAULT = 'cm';

    private $contactInformation;

    public function parent(): HasOne
    {
        return $this->hasOne(__CLASS__, 'id', 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function contactInformation()
    {
        return $this->morphOne(ContactInformation::class, 'object')->withTrashed();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->using(CustomerUser::class)->withPivot(['role_id']);
    }

    public function orderChannels()
    {
        return $this->hasMany(OrderChannel::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderStatuses()
    {
        return $this->hasMany(OrderStatus::class);
    }

    public function shippingCarriers()
    {
        return $this->hasMany(ShippingCarrier::class);
    }

    public function shippingMethods()
    {
        return $this->hasManyThrough(ShippingMethod::class, ShippingCarrier::class);
    }

    public function shippingBoxes()
    {
        return $this->hasMany(ShippingBox::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function purchaseOrdersStatuses()
    {
        return $this->hasMany(PurchaseOrderStatus::class);
    }

    public function returns()
    {
        return $this->hasMany(Return_::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function taskTypes()
    {
        return $this->hasMany(TaskType::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function hasUser($userId)
    {
        return $this->users->contains('id', $userId);
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function webshipperCredentials()
    {
        return $this->hasMany(WebshipperCredential::class);
    }

    public function easypostCredentials()
    {
        return $this->hasMany(EasypostCredential::class);
    }

    public function orderSlipLogo()
    {
        return $this->morphOne(Image::class, 'object');
    }

    public function printers()
    {
        return $this->hasMany(Printer::class);
    }

    public function labelPrinter()
    {
        return Printer::find(customer_settings($this->id, CustomerSetting::CUSTOMER_SETTING_LABEL_PRINTER_ID));
    }

    public function barcodePrinter()
    {
        return Printer::find(customer_settings($this->id, CustomerSetting::CUSTOMER_SETTING_BARCODE_PRINTER_ID));
    }

    public function slipPrinter()
    {
        return Printer::find(customer_settings($this->id, CustomerSetting::CUSTOMER_SETTING_SLIP_PRINTER_ID));
    }

    public function locationTypes(): HasMany
    {
        return $this->hasMany(LocationType::class);
    }

    public function parentWarehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class, 'customer_id', 'parent_id');
    }

    public function getWarehouses(): Collection
    {
        return $this->warehouses->merge($this->parentWarehouses);
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id) ?? false;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function billingDetails(): HasOne
    {
        return $this->hasOne(BillingDetails::class);
    }
}
