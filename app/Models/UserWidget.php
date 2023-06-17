<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\UserWidget
 *
 * @property int $id
 * @property int $user_id
 * @property string $location
 * @property string $grid_stack_data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|UserWidget newModelQuery()
 * @method static Builder|UserWidget newQuery()
 * @method static Builder|UserWidget query()
 * @method static Builder|UserWidget whereCreatedAt($value)
 * @method static Builder|UserWidget whereGridStackData($value)
 * @method static Builder|UserWidget whereId($value)
 * @method static Builder|UserWidget whereLocation($value)
 * @method static Builder|UserWidget whereUpdatedAt($value)
 * @method static Builder|UserWidget whereUserId($value)
 * @mixin \Eloquent
 */
class UserWidget extends Model
{
    public CONST WIDGET_LIST = [
//        '[widget_orders_received]' => [
//            'view' => 'shared.draggable.widgets.orders_received',
//            'title' => 'Orders Received',
//        ],
//        '[widget_orders_shipped]' => [
//            'view' => 'shared.draggable.widgets.orders_shipped',
//            'title' => 'Orders Shipped'
//        ],
//        '[widget_returns]' => [
//            'view' => 'shared.draggable.widgets.returns',
//            'title' => 'Returns'
//        ],
//        '[widget_orders_late]' => [
//            'view' => 'shared.draggable.widgets.orders_late',
//            'title' => 'Late Orders'
//        ],
        '[widget_revenue_over_time]' => [
            'view' => 'shared.draggable.widgets.revenue_over_time',
            'title' => 'Revenue Over Time'
        ],
        '[widget_order_by_country]' => [
            'view' => 'shared.draggable.widgets.map',
            'title' => 'Orders By Country'
        ],
//        '[widget_average_lead_time_orders]' => [
//            'view' => 'shared.draggable.widgets.average_lead_time_orders',
//            'title' => 'Average lead time on orders from created to shipped'
//        ],
//        '[widget_purchase_orders_coming_in]' => [
//            'view' => 'shared.draggable.widgets.purchase_orders_coming_in',
//            'title' => 'Purchase Orders Received'
//        ],
        '[widget_purchase_orders_received]' => [
            'view' => 'shared.draggable.widgets.purchase_orders_received',
            'title' => 'Purchase orders coming in'
        ],
//        '[widget_purchase_orders_received_quantity]' => [
//            'view' => 'shared.draggable.widgets.purchase_orders_received_quantity',
//            'title' => 'Purchase orders received quantity'
//        ],
        '[widget_sales_info]' => [
            'view' => 'shared.draggable.widgets.sales',
            'title' => 'Orders sales info'
        ],
        '[widget_top_selling_items]' => [
            'view' => 'shared.draggable.widgets.top_selling_items',
            'title' => 'Top selling items'
        ],
        '[widget_info_tabs]' => [
            'view' => 'shared.draggable.widgets.info_tabs',
            'title' => 'Info tabs'
        ],

        // orders received

//        '[widget_orders_table]' => 'shared.draggable.widgets.orders_table',
//        '[widget_orders_table_dashboard]' => 'shared.draggable.widgets.orders_table_dashboard',
//        '[widget_revenue]' => 'shared.draggable.widgets.revenue',
//        '[widget_items_shipped]' => 'shared.draggable.widgets.total_items_shipped',
//        '[widget_warehouse_space_used]' => 'shared.draggable.widgets.warehouse_space_used',
//        '[widget_average_lead_time_returns]' => 'shared.draggable.widgets.average_lead_time_returns',
//        '[widget_purchase_orders_coming_in]' => 'shared.draggable.widgets.purchase_orders_coming_in',
//        '[widget_purchase_orders_received]' => 'shared.draggable.widgets.purchase_orders_received',
    ];

    public const DEFAULT_DAY_DIFF = 7;
    public const DEFAULT_MONTH_DIFF = 3;
    public const SHOW_GEO_WIDGET = true;

    protected $fillable = ['location', 'grid_stack_data', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
