<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerSetting
 *
 * @property int $id
 * @property int $customer_id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|CustomerSetting newModelQuery()
 * @method static Builder|CustomerSetting newQuery()
 * @method static Builder|CustomerSetting query()
 * @method static Builder|CustomerSetting whereCreatedAt($value)
 * @method static Builder|CustomerSetting whereDeletedAt($value)
 * @method static Builder|CustomerSetting whereId($value)
 * @method static Builder|CustomerSetting whereKey($value)
 * @method static Builder|CustomerSetting whereUpdatedAt($value)
 * @method static Builder|CustomerSetting whereUserId($value)
 * @method static Builder|CustomerSetting whereValue($value)
 * @mixin \Eloquent
 */
class CustomerSetting extends Model
{
    public const CUSTOMER_SETTING_WEIGHT_UNIT = 'weight_unit';
    public const CUSTOMER_SETTING_DIMENSIONS_UNIT = 'dimensions_unit';
    public const CUSTOMER_SETTING_ORDER_SLIP_HEADING = 'order_slip_heading';
    public const CUSTOMER_SETTING_ORDER_SLIP_TEXT = 'order_slip_text';
    public const CUSTOMER_SETTING_ORDER_SLIP_FOOTER = 'order_slip_footer';
    public const CUSTOMER_SETTING_ORDER_SLIP_AUTO_PRINT = 'order_slip_auto_print';
    public const CUSTOMER_SETTING_LABEL_PRINTER_ID = 'label_printer_id';
    public const CUSTOMER_SETTING_BARCODE_PRINTER_ID = 'barcode_printer_id';
    public const CUSTOMER_SETTING_SLIP_PRINTER_ID = 'slip_printer_id';
    public const CUSTOMER_SETTING_LOCALE = 'locale';
    public const CUSTOMER_SETTING_CUSTOMER_CSS = 'customer_css';
    public const CUSTOMER_SETTING_CURRENCY = 'currency';
    public const CUSTOMER_SETTING_SHIPPING_BOX_ID = 'shipping_box_id';
    public const CUSTOMER_SETTING_USE_ZPL_LABELS = 'use_zpl_labels';
    public const CUSTOMER_SETTING_LABEL_SIZE_WIDTH = 'label_size_width';
    public const CUSTOMER_SETTING_LABEL_SIZE_HEIGHT = 'label_size_height';
    public const CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH = 'document_size_width';
    public const CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT = 'document_size_height';
    public const CUSTOMER_SETTING_BARCODE_SIZE_WIDTH = 'barcode_size_width';
    public const CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT = 'barcode_size_height';
    public const CUSTOMER_SETTING_LOT_DISABLE_PICKING_DAYS = 'lot_disable_picking_days';
    public const CUSTOMER_SETTING_LOT_NOTIFICATION_EMAIL = 'lot_notification_email';
    public const CUSTOMER_SETTING_MAX_AMOUNT_TO_PICK = 'max_amount_to_pick';
    public const CUSTOMER_SETTING_AUTO_RETURN_LABEL = 'auto_return_label';
    public const CUSTOMER_SETTING_SHOW_PRICES_ON_SLIPS = 'show_prices_on_slips';
    public const CUSTOMER_SETTING_PICKING_ROUTE_STRATEGY = 'picking_route_strategy';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_ORDERS = 'disable_autoload_orders';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS = 'disable_autoload_products';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_ORDER_ITEMS = 'disable_autoload_products_order_items';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_ORDERS_SHIPPED = 'disable_autoload_products_orders_shipped';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_TOTE_ITEMS = 'disable_autoload_products_tote_items';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_SINGLE_ORDER_PACKING = 'disable_autoload_single_order_packing';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_INVENTORY_CHANGE_LOG = 'disable_autoload_inventory_change_log';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_SHIPMENT_REPORT = 'disable_autoload_shipment_report';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PICKER_REPORT = 'disable_autoload_picker_report';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_PACKER_REPORT = 'disable_autoload_packer_report';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_SHIPPED_ITEMS_REPORT = 'disable_autoload_shipped_item_report';
    public const CUSTOMER_SETTING_DISABLE_AUTOLOAD_ALLOW_LOAD_BUTTON = 'disable_autoload_allow_load_button';
    public const CUSTOMER_SETTING_DEFAULT_RETURN_SHIPPING_METHOD = 'default_return_shipping_method';

    public const CUSTOMER_SETTING_KEYS = [
        self::CUSTOMER_SETTING_WEIGHT_UNIT,
        self::CUSTOMER_SETTING_DIMENSIONS_UNIT,
        self::CUSTOMER_SETTING_ORDER_SLIP_HEADING,
        self::CUSTOMER_SETTING_ORDER_SLIP_TEXT,
        self::CUSTOMER_SETTING_ORDER_SLIP_FOOTER,
        self::CUSTOMER_SETTING_ORDER_SLIP_AUTO_PRINT,
        self::CUSTOMER_SETTING_LABEL_PRINTER_ID,
        self::CUSTOMER_SETTING_BARCODE_PRINTER_ID,
        self::CUSTOMER_SETTING_SLIP_PRINTER_ID,
        self::CUSTOMER_SETTING_LOCALE,
        self::CUSTOMER_SETTING_CUSTOMER_CSS,
        self::CUSTOMER_SETTING_CURRENCY,
        self::CUSTOMER_SETTING_SHIPPING_BOX_ID,
        self::CUSTOMER_SETTING_USE_ZPL_LABELS,
        self::CUSTOMER_SETTING_LABEL_SIZE_WIDTH,
        self::CUSTOMER_SETTING_LABEL_SIZE_HEIGHT,
        self::CUSTOMER_SETTING_DOCUMENT_SIZE_WIDTH,
        self::CUSTOMER_SETTING_DOCUMENT_SIZE_HEIGHT,
        self::CUSTOMER_SETTING_BARCODE_SIZE_WIDTH,
        self::CUSTOMER_SETTING_BARCODE_SIZE_HEIGHT,
        self::CUSTOMER_SETTING_LOT_DISABLE_PICKING_DAYS,
        self::CUSTOMER_SETTING_LOT_NOTIFICATION_EMAIL,
        self::CUSTOMER_SETTING_MAX_AMOUNT_TO_PICK,
        self::CUSTOMER_SETTING_AUTO_RETURN_LABEL,
        self::CUSTOMER_SETTING_SHOW_PRICES_ON_SLIPS,
        self::CUSTOMER_SETTING_PICKING_ROUTE_STRATEGY,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_ORDERS,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_ORDER_ITEMS,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_ORDERS_SHIPPED,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PRODUCTS_TOTE_ITEMS,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_SINGLE_ORDER_PACKING,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_INVENTORY_CHANGE_LOG,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_SHIPMENT_REPORT,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PICKER_REPORT,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_PACKER_REPORT,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_SHIPPED_ITEMS_REPORT,
        self::CUSTOMER_SETTING_DISABLE_AUTOLOAD_ALLOW_LOAD_BUTTON,
        self::CUSTOMER_SETTING_DEFAULT_RETURN_SHIPPING_METHOD
    ];

    protected $fillable = ['customer_id', 'key', 'value'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
