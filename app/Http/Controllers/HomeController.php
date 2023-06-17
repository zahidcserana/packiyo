<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Exports\ProductExport;
use App\Exports\PurchaseOrderExport;
use App\Exports\ReturnExport;
use App\Exports\ShipmentExport;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\UserSetting;
use App\Models\UserWidget;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $settings = [
            'dashboard_filter_date_start' => user_settings(UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_START),
            'dashboard_filter_date_end' => user_settings(UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_END)
        ];

        $showGeoWidget = UserWidget::SHOW_GEO_WIDGET;
        $defaultDayDif = UserWidget::DEFAULT_DAY_DIFF;
        $defaultMonthDif = UserWidget::DEFAULT_MONTH_DIFF;
        $dashboardFilterDateStart = Carbon::now()->subWeeks(2)->format('Y-m-d');//$settings['dashboard_filter_date_start'];
        $dashboardFilterDateEnd = Carbon::now()->format('Y-m-d');  //$settings['dashboard_filter_date_end'];

        return view('dashboard.index', [
            'showGeoWidget'=>$showGeoWidget,
            'defDayDif'=>$defaultDayDif,
            'dashboardFilterDateStart' => $dashboardFilterDateStart,
            'dashboardFilterDateEnd' => $dashboardFilterDateEnd
        ]);
    }

    public function totalRevenue(Request $request): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();
        $dashboardSettings = $this->dashboardSettings();
        $from = $request->get('startDate') ?? Carbon::createFromTimestamp(0)->toDateString();//$dateVariables['from'];
        $to = $request->get('endDate') ?? Carbon::now()->format('Y-m-d');//$dateVariables['to'];

        $orders = Order::whereIn('customer_id', $customerIds)
            ->whereBetween('ordered_at', [$from, $to]);

        if (!empty($dashboardSettings->dashboard_filter_status)) {
            $orders = $orders->where('status', $dashboardSettings->dashboard_filter_status);
        }

        $dateDiffDays = Carbon::parse($from)->diffInDays(Carbon::parse($to));

        if ($dateDiffDays <= 31) {

            $orders = $orders
                ->select(
                    'id',
                    DB::raw("date(ordered_at) as date"),
                    DB::raw("sum(subtotal) as revenue"),
                    DB::raw("count(id) as orders")
                )
                ->groupBy('date');
        } else {
            $orders = $orders
                ->select(
                    'id',
                    DB::raw("MONTH(ordered_at) as month"),
                    DB::raw("date(ordered_at) as date"),
                    DB::raw("sum(subtotal) as revenue"),
                    DB::raw("count(id) as orders")
                )
                ->groupBy('month');
        }

        $currency = '';
        /*$customer = Customer::whereId($customerIds)->first();
        if ($customer) {
            $currency = $customer->currency_code ?? '';
        }*/

        return response()->json(['data' => $orders->get(), 'currency' => $currency]);
    }

    public function totalShippedOrders()
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        $orders = Order::whereIn('customer_id', $customerIds)
            ->whereBetween('ordered_at', [$dateVariables['from'], $dateVariables['to']])
            ->has('shipments')
            ->withCount('shipments');

        return $orders->count();
    }

    public function totalShippedItems()
    {
        $customerIds = Auth()->user()->customerIds();

        $ordersThatHaveShipmentsIds = Order::whereIn('customer_id', $customerIds)
            ->has('shipments');

        $ordersThatHaveShipmentsIds = $ordersThatHaveShipmentsIds
            ->get()
            ->pluck('id')
            ->toArray();

        $shipments = Shipment::whereIn('order_id', $ordersThatHaveShipmentsIds)
            ->withCount('shipmentItems')
            ->get();

        return $shipments->sum('shipment_items_count');
    }

    public function ordersByCountry(Request $request): JsonResponse
    {
        $dateVariables = $this->dateVariables();
        $dashboardSettings = $this->dashboardSettings();

        $from = $request->get('startDate') ?? Carbon::createFromTimestamp(0)->toDateString();//$dateVariables['from'];
        $to = $request->get('endDate') ?? Carbon::now()->format('Y-m-d');//$dateVariables['to'];

        $countries = app()->order->search(null)
            ->whereBetween('orders.ordered_at', [$from, $to]);

        if (!empty($dashboardSettings->dashboard_filter_status)) {
            //$countries = $countries->where('status', $dashboardSettings->dashboard_filter_status);
        }

        $countries
            ->join('contact_informations AS shipping_contact_information', 'shipping_contact_information_id', '=', 'shipping_contact_information.id')
            ->join('countries', 'shipping_contact_information.country_id', '=', 'countries.id')
            ->where('shipping_contact_information.object_type', Order::class)
            ->select(
                'orders.id',
                DB::raw('count(countries.id) as total_orders, countries.name, countries.iso_3166_2')
            )
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->groupBy('countries.id');

        return response()->json($countries->get());
    }

    public function ordersByCities(Request $request): JsonResponse
    {
        $dateVariables = $this->dateVariables();
        $dashboardSettings = $this->dashboardSettings();

        $from = $request->get('startDate') ?? Carbon::createFromTimestamp(0)->toDateString();//$dateVariables['from'];
        $to = $request->get('endDate') ?? Carbon::now()->format('Y-m-d');//$dateVariables['to'];

        $orders = app()->order->search(null)->whereBetween('orders.ordered_at', [$from, $to]);

        if (!empty($dashboardSettings->dashboard_filter_status)) {
            //$orders = $orders->where('status', $dashboardSettings->dashboard_filter_status);
        }

        $orders->whereNotNull(['shipping_lat', 'shipping_lng'])
            ->groupBy(['shipping_lat', 'shipping_lng'])
            ->leftJoin('contact_informations', 'orders.shipping_contact_information_id', '=', 'contact_informations.id')
            ->leftJoin('countries', 'contact_informations.country_id', '=', 'countries.id')
            ->select(
                'orders.id',
                'orders.shipping_lat as lat',
                'orders.shipping_lng as lng',
                DB::raw('count(orders.id) as count'),
                'contact_informations.city as title',
                'countries.iso_3166_2 as countryCode'
            );

        if (Route::current()->uri() === 'orders/orders_by_cities_limited') {
            $limitemOrders = $orders
                ->orderBy('total_orders', 'desc')
                ->limit(10)
                ->select(
                    'orders.id',
                    DB::raw('count(orders.id) as total_orders'),
                    'contact_informations.city as title',
                    'countries.iso_3166_2 as countryCode'
                );

            return response()->json($limitemOrders->get());
        }

        return response()->json($orders->get());
    }

    public function globalSearch(Request $request)
    {
        $term = $request->input('term');
        $results = [];

        $orders = app()->order->search($term)->get();
        $inventory = app()->product->search($term)->get();
        $purchaseOrders = app()->purchaseOrder->search($term)->get();
        $returns = app()->return->search($term)->get();
        $shipments = app()->shipment->search($term)->get();

        if ($inventory->count()) {
            $inventory = $inventory->splice(0, 5);
            $inventory['linkToTable'] = route('products.index', ['term' => $term]);
            $inventory['count'] = $inventory->count();
            $results['Inventory'] = $inventory;
        }

        if ($orders->count()) {
            $orders = $orders->splice(0, 3);
            $orders['linkToTable'] = route('orders.index', ['term' => $term]);
            $orders['count'] = $orders->count();
            $results['Orders'] = $orders;
        }

        if ($returns->count()) {
            $returns = $returns->splice(0, 3);
            $returns['linkToTable'] = route('returns.index', ['term' => $term]);
            $returns['count'] = $returns->count();
            $results['Returns'] = $returns;
        }

        if ($shipments->count()) {
            $shipments = $shipments->splice(0, 3);
            $shipments['linkToTable'] = route('shipments.index', ['term' => $term]);
            $shipments['count'] = $shipments->count();
            $results['Shipments'] = $shipments;
        }

        if ($purchaseOrders->count()) {
            $purchaseOrders = $purchaseOrders->splice(0, 3);
            $purchaseOrders['linkToTable'] = route('purchase_orders.index', ['term' => $term]);
            $purchaseOrders['count'] = $purchaseOrders->count();
            $results['PurchaseOrders'] = $purchaseOrders;
        }

        return response(view('shared.global_search.results', ['results' => $results]));
    }

    public function ordersReceivedCalc(): JsonResponse
    {
        $dashboardSettings = $this->dashboardSettings();
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        $todays = Order::whereIn('customer_id', $customerIds)
            ->whereBetween('ordered_at', [($dateVariables['from']), $dateVariables['to']]);


        if (!empty($dashboardSettings->dashboard_filter_status)) {
            //$todays = $todays->where('status', $dashboardSettings->dashboard_filter_status);
        }

        $todays = $todays->count();

        return response()->json($todays);
    }

    public function returnsCalc(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        $todays = Return_::whereBetween('requested_at', [$dateVariables['from'], $dateVariables['to']])
            ->whereHas('order', function ($q) use ($customerIds) {
                $q->whereIn('orders.customer_id', $customerIds);
            })
            ->count();

        return response()->json($todays ?? 0);
    }

    public function shipmentsCalc(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        // TODO: check customer
        $shipments = Shipment::whereBetween('ordered_at', [$dateVariables['from'], $dateVariables['to']]);

        return response()->json($shipments->count());
    }

    public function purchaseOrdersCalc(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        $purchaseOrders = PurchaseOrder::whereBetween('ordered_at', [$dateVariables['from'], $dateVariables['to']])
            ->whereIn('customer_id', $customerIds);

        return response()->json(['poCount' => $purchaseOrders->count()]);
    }

    public function purchaseOrdersQuantityCalc(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();
        $purchaseOrders = PurchaseOrder::whereBetween('ordered_at', [$dateVariables['from'], $dateVariables['to']])
            ->whereIn('customer_id', $customerIds)->get();

        $totalProductsReceived = [];

        foreach ($purchaseOrders as $purchaseOrder) {
            $totalProductsReceived[] = $purchaseOrder->purchaseOrderItems->sum('quantity_received');
        }

        return response()->json(['poQuantityReceived' => array_sum($totalProductsReceived)]);
    }

    public function purchaseOrdersReceived(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $purchaseOrdersCollection = app()->purchaseOrder->search(null)
            ->whereNull('received_at')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->leftJoin('contact_informations AS supplier_contact_information', function ($join) {
                $join->on('suppliers.id', '=', 'supplier_contact_information.object_id')
                    ->where('supplier_contact_information.object_type', Supplier::class);
            })
            ->limit(5)
            ->whereIn('suppliers.customer_id', $customerIds)
            ->select('*', 'purchase_orders.*');

        return response()->json([
            'data' => $purchaseOrdersCollection->get()->toArray(),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    public function lateOrders(): JsonResponse
    {
        $customerIds = Auth()->user()->customerIds();
        $dateVariables = $this->dateVariables();

        $orders = Order::whereHas("orderItems", function($q){
            $q->where('quantity_pending', '>', 0);
        })
        ->limit(5)
        ->select(
                'id',
                'number',
                DB::raw("DATE_FORMAT(required_shipping_date_at, '%Y-%m-%d') as required_shipping_date_formatted")
            )
        ->whereIn('customer_id', $customerIds)
        ->whereBetween('ordered_at', [$dateVariables['from'], $dateVariables['to']])
        ->whereDate('required_shipping_date_at', '<', $dateVariables['tomorrow']);

        /*$orders = Order::whereIn('customer_id', $customerIds)
            ->whereNotNull('required_shipping_date_at')
            ->whereNotIn('status', [Order::STATUS_FULFILLED, Order::STATUS_CANCELLED])
            ->whereDate('required_shipping_date_at', '<', $dateVariables['tomorrow'])
            ->limit(5)
            ->select(
                'id',
                'number',
                DB::raw("DATE_FORMAT(orders.required_shipping_date_at, '%Y-%m-%d') as required_shipping_date_formatted")
            );*/

        $orders = $orders->get();

        return response()->json([
            'data' => $orders->toArray(),
            'recordsTotal' => $orders->count(),
            'recordsFiltered' => $orders->count(),
        ]);
    }



    public function dashboardSettings(): array
    {
        return [
            'dashboard_filter_date_start' => user_settings(UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_START),
            'dashboard_filter_date_end' => user_settings(UserSetting::USER_SETTING_DASHBOARD_FILTER_DATE_END)
        ];
    }

    public function dateVariables(): array
    {
        $yesterday = Carbon::yesterday()->toDateTimeString();
        $today = Carbon::now()->subDays(env('DEFAULT_DASHBOARD_DATE_RANGE'))->toDateTimeString();
        $tomorrow = Carbon::tomorrow()->toDateTimeString();
        $dashboardSettings = $this->dashboardSettings();

        $from = empty($dashboardSettings['dashboard_filter_date_start'])
            ? $today : date($dashboardSettings['dashboard_filter_date_start']);
        $to =   empty($dashboardSettings['dashboard_filter_date_end'])
            ? $tomorrow : Carbon::parse($dashboardSettings['dashboard_filter_date_end'])->addDay()->toDate()->format('Y-m-d');

        $formatted_from = Carbon::parse($from)->timezone('UTC')->toDateTimeString();
        $formatted_to = Carbon::parse($to)->timezone('UTC')->toDateTimeString();

        return [
            'yesterday' => $yesterday,
            'today' => $today,
            'tomorrow' => $tomorrow,
            'from' => $formatted_from,
            'to' => $formatted_to,
        ];
    }

    public function export(Request $request)
    {
        $data = $request->data ?? null;

        $model = $request->model ?? null;

        if ($model == 'product') {
            return new ProductExport($data);
        } else if ($model == 'order') {
            return new OrderExport($data);
        } else if ($model == 'purchaseOrder') {
            return new PurchaseOrderExport($data);
        } else if ($model == 'return') {
            return new ReturnExport($data);
        }else if ($model == 'shipment') {
            return new ShipmentExport($data);
        }

        return redirect()->back()->withErrors(__('Something went wrong!.'));
    }
}
