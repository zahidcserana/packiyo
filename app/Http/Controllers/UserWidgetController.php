<?php

namespace App\Http\Controllers;

use App\Http\Dto\Widgets\DashboardInfoWidgetDto;
use App\Http\Dto\Widgets\DashboardInfoWidgetOrdersDto;
use App\Http\Dto\Widgets\DashboardInfoWidgetProductsDto;
use App\Http\Dto\Widgets\DashboardInfoWidgetPurchasesDto;
use App\Http\Dto\Widgets\DashboardInfoWidgetShipmentsDto;
use App\Http\Dto\Widgets\DashboardSalesWidgetDto;
use App\Models\Order;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Shipment;
use App\Models\UserWidget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class UserWidgetController extends Controller
{
    public function getWidgets(Request $request) {
        $DBWidget = UserWidget::where('user_id', Auth::user()->id)
        ->where('location', $request->location)->first();

        if (!empty($DBWidget) && $DBWidget->exists()) {
            $widgets = $DBWidget->grid_stack_data ?? '';
        } else {
            $widgetsPath = base_path() . '/config/widgets.custom.json';

            if (!file_exists($widgetsPath)) {
                $widgetsPath = base_path() . '/config/widgets.json';
            }

            $widgets = file_get_contents($widgetsPath);
        }

        return response()->json($widgets ?? '');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getDashboardSalesWidget(Request $request): \Illuminate\Contracts\View\View
    {
        $customer = app()->user->getSelectedCustomers();

        $customers = $customer->pluck('id')->toArray();
        $startDate = $request->get('startDate') ?? Carbon::createFromTimestamp(0)->toDateString();
        $endDate = $request->get('endDate') ?? Carbon::now()->format('Y-m-d');

        $orders = Order::query()
            ->whereIn('customer_id', $customers)
            ->whereBetween('orders.ordered_at', [$startDate, $endDate]);

        $ordersTotalPrice = $orders->sum('total');
        $ordersTotalCount = $orders->count();
        $ordersAvgPrice = $orders->avg('total');
        $ordersUnitsSold = $orders->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')->sum('quantity');
        $data = new DashboardSalesWidgetDto($ordersTotalPrice, $ordersUnitsSold, $ordersTotalCount, $ordersAvgPrice);

        return View::make('shared.draggable.widgets.components.sales', ['data' => $data]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getDashboardTopSellingWidget(Request $request): \Illuminate\Contracts\View\View
    {
        $customer = app()->user->getSelectedCustomers();

        $startDate = $request->get('startDate') ?? Carbon::createFromTimestamp(0)->toDateString();
        $endDate = $request->get('endDate') ?? Carbon::now()->format('Y-m-d');

        $topSellingItems = Product::query()->whereBetween('order_items.created_at', [$startDate, $endDate])
            ->limit(3)
            ->leftJoin('order_items', 'order_items.product_id','=', 'products.id')
            ->groupBy('products.id')
            ->select('products.*')
            ->orderByDesc(DB::raw('sum(order_items.quantity)'));

        $customers = $customer->pluck('id')->toArray();

        $topSellingItems = $topSellingItems->whereIn('customer_id', $customers);

        return View::make('shared.draggable.widgets.components.top_selling_items', ['topSellingItems' => $topSellingItems->get()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getDashboardInfoWidget(Request $request): \Illuminate\Contracts\View\View
    {
        $customers = app()->user->getSelectedCustomers();
        $customerIds = $customers->pluck('id')->toArray();

        $ordersToday = Order::query()->whereIn('customer_id', $customerIds)->where('ordered_at', '>=', Carbon::now()->subDay())->count();
        $ordersConfirm = Order::query()->whereIn('customer_id', $customerIds)->whereNotNull('deleted_at')->count();
        $ordersToShip = Order::query()->whereIn('customer_id', $customerIds)->where('ready_to_ship', '1')->count();
        $ordersComplete = Order::query()->whereIn('customer_id', $customerIds)->whereHas('shipments', function ($q) {
            $q->where('processing_status', Shipment::PROCESSING_STATUS_SUCCESS);
        })->count();

        $productUniqueOrders = $ordersConfirm;
        $productBackordered= Product::query()->whereIn('customer_id', $customerIds)->where('quantity_backordered','!=', '0')->count();
        $productPieces = Product::query()->whereIn('customer_id', $customerIds)->whereDoesntHave('kitItems')->count();
        $productUniqueSkus = Product::query()->whereIn('customer_id', $customerIds)->distinct('sku')->count();

        $shipmentsToday = Shipment::query()
            ->leftJoin('orders', 'orders.id', '=', 'shipments.order_id')
            ->where('shipments.created_at', '>=', Carbon::now()->subDay())
            ->whereIn('customer_id', $customerIds)
            ->groupBy('shipments.id')
            ->count();
        $shipmentsYesterday = Shipment::query()
            ->leftJoin('orders', 'orders.id', '=', 'shipments.order_id')
            ->whereBetween('shipments.created_at', [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()])
            ->whereIn('customer_id', $customerIds)
            ->groupBy('shipments.id')
            ->count();
        $shipmentsLastWeek = Shipment::query()
            ->leftJoin('orders', 'orders.id', '=', 'shipments.order_id')
            ->where('shipments.created_at', '>=', Carbon::now()->subWeek())
            ->whereIn('customer_id', $customerIds)
            ->groupBy('shipments.id')
            ->count();
        $shipmentsLastMonth = Shipment::query()
            ->leftJoin('orders', 'orders.id', '=', 'shipments.order_id')
            ->where('shipments.created_at', '>=', Carbon::now()->subMonth())
            ->whereIn('customer_id', $customerIds)
            ->groupBy('shipments.id')
            ->count();

        $purchasesOpen = PurchaseOrder::query()->whereIn('customer_id', $customerIds)->whereNull(['deleted_at', 'closed_at'])->count();
        $purchasesComplete = PurchaseOrder::query()->whereIn('customer_id', $customerIds)->whereNotNull('closed_at')->count();
        $purchasesOpenItems = PurchaseOrderItem::query()
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->whereIn('customer_id', $customerIds)
            ->where('quantity_pending','!=', 0)
            ->sum('quantity_pending');
        $purchasesCompletedItems = PurchaseOrderItem::query()
            ->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->whereIn('customer_id', $customerIds)
            ->where('quantity_received','!=', 0)
            ->sum('quantity_received');

        $orders = new DashboardInfoWidgetOrdersDto($ordersToday, $ordersConfirm, $ordersToShip, $ordersComplete);
        $products = new DashboardInfoWidgetProductsDto($productUniqueOrders, $productBackordered, $productPieces, $productUniqueSkus);
        $shipments = new DashboardInfoWidgetShipmentsDto($shipmentsToday, $shipmentsYesterday, $shipmentsLastWeek, $shipmentsLastMonth );
        $purchases = new DashboardInfoWidgetPurchasesDto($purchasesOpen, $purchasesComplete, $purchasesOpenItems, $purchasesCompletedItems);

        $data = new DashboardInfoWidgetDto($orders, $products, $shipments, $purchases);

        return View::make('shared.draggable.widgets.components.info_tabs', ['data' =>  $data]);
    }

    public function createUpdate(Request $request) {
        return UserWidget::updateOrCreate(
            ['location' => $request->location, 'user_id' => Auth::user()->id],
            ['grid_stack_data' => $request->grid_stack_data]
        );
    }
}
