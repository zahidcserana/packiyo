<?php

namespace App\Http\Controllers;

use App\Http\Dto\Filters\OrdersDataTableDto;
use App\Http\Requests\Shipment\ReShipRequest;
use App\Http\Requests\Csv\{ExportCsvRequest, ImportCsvRequest};
use App\Http\Requests\Order\{BulkSelectionRequest,
    BulkEditRequest,
    DestroyRequest,
    StoreRequest,
    UpdateRequest};
use App\Http\Resources\OrderTableResource;
use App\Models\{Currency,
    Customer,
    CustomerSetting,
    Order,
    OrderItem,
    OrderStatus,
    ShippingCarrier,
    ShippingMethod,
    Warehouse};
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\View;
use App\Http\Requests\Order\StoreReturnRequest as StoreOrderReturnRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class);
    }

    public function index($keyword='')
    {
        $customers = app('user')->getSelectedCustomers();
        $customerIds = $customers->pluck('id')->toArray();

        $shippingCarriers = ShippingCarrier::whereIn('customer_id', $customerIds)->get();

        $data = new OrdersDataTableDto(
            $customers,
            OrderStatus::whereIn('customer_id', $customerIds)->get(),
            $shippingCarriers->pluck('name')->unique(),
            ShippingMethod::whereIn('shipping_carrier_id', $shippingCarriers->pluck('id'))->get()->pluck('name')->unique()
        );

        return view('orders.index', [
            'page' => 'manage_orders',
            'keyword' => $keyword,
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('orders'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'ordered_at';
        $sortDirection = 'desc';
        $filterInputs = $request->get('filter_form');
        $term = $request->get('search')['value'];

        if ($term) {
            $filterInputs = [];
        }

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $orderCollection = app('order')->getQuery($filterInputs);

        $orderCollection = $orderCollection->orderBy(trim($sortColumnName), $sortDirection);

        if ($term) {
            $orderCollection = app('order')->searchQuery($term, $orderCollection);
        }

        $start = $request->get('start');
        $length = $request->get('length');

        if ($length == -1) {
            $length = 10;
        }

        if ($length) {
            $orderCollection = $orderCollection->skip($start)->limit($length);
        }

        $orders = $orderCollection->get();
        $visibleFields = app('editColumn')->getVisibleFields('orders');

        $orderCollection = OrderTableResource::collection($orders);

        return response()->json([
            'data' => $orderCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
         ]);
    }

    public function create()
    {
        return view('orders.create',[
            'page' => 'create_order',
        ]);
    }

    public function store(StoreRequest $request)
    {
        app('order')->store($request);

        return redirect()->route('order.index')->withStatus(__('Order successfully created.'));
    }

    public function edit(Order $order)
    {
        $customer = $order->customer;

        $orderStatuses = $customer->orderStatuses;

        $orderStatuses->prepend([
            'id' => 'pending',
            'name' => 'Pending'
        ]);

        $partiallyShipped = app('order')->isOrderPartiallyShipped($order);
        $shippingMethods = app('shippingMethodMapping')->filterShippingMethods($customer);

        $order = $order->load([
            'orderItems.product.kitItems',
            'orderItems.kitOrderItems',
            'orderItems.toteOrderItems.user',
            'orderItems.toteOrderItems.pickingBatchItem',
            'shipments'
        ]);

        $shipmentItemLots = [];
        foreach ($order->shipments as $shipment) {

            foreach ($shipment->shipmentItems as $shipmentItem) {

                $shipmentItemLots[$shipmentItem->id] = '';

                $shipmentItem = $shipmentItem->load(['orderItem.packageOrderItems.package' => function ($query) use ($shipment) {
                    return $query->where('shipment_id', $shipment->id);
                }]);

                foreach ($shipmentItem->orderItem->packageOrderItems as $packageOrderItem) {

                    if (!is_null($packageOrderItem->package) && $packageOrderItem->package->shipment_id == $shipment->id && !is_null($packageOrderItem->lot)) {
                        $shipmentItemLots[$shipmentItem->id] .= $packageOrderItem->lot->name . ' ';
                    }
                }
            }
        }

        return view('orders.edit', [
            'order' => $order,
            'page' => 'manage_orders',
            'orderStatuses' => $orderStatuses,
            'shippingMethods' => $shippingMethods,
            'partiallyShipped' => $partiallyShipped,
            'audits' => $order->getAllAudits(),
            'shipmentItemLots' => $shipmentItemLots,
            'currency' => $order->currency->symbol ?? Currency::find(customer_settings($order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY))->symbol ?? ''
        ]);
    }

    public function getOrderReturnForm(Order $order): \Illuminate\Contracts\View\View
    {
        $customer = $order->customer;
        $orderStatuses = $customer->orderStatuses;

        $orderStatuses->prepend([
            'id' => 'pending',
            'name' => 'Pending'
        ]);

        $shippedOrderItems = app('order')->getShippedOrderItems($order);
        $warehouse = Warehouse::where('customer_id', $customer->parent_id ? $customer->parent_id : $customer->id)->first();
        $shippingMethods = app('shippingMethodMapping')->filterShippingMethods($customer);
        $returnShippingMethod = app('shippingMethodMapping')->returnShippingMethod($order);

        return View::make('shared.modals.components.orders.returnForm', [
            'order' => $order->load('orderItems.product.kitItems', 'orderItems.kitOrderItems', 'orderItems.toteOrderItems'),
            'page' => 'manage_orders',
            'shippedOrderItems' => $shippedOrderItems,
            'orderStatuses' => $orderStatuses,
            'defaultReturnStatus' => [
                'id' => $orderStatuses->first()['id'] ?? '',
                'text' => $orderStatuses->first()['name'] ?? ''
            ],
            'defaultWarehouse' =>  [
                'id' => $warehouse->id,
                'text' => $warehouse->contactInformation->name . ', ' . $warehouse->contactInformation->email . ', ' . $warehouse->contactInformation->zip . ', ' . $warehouse->contactInformation->city
            ],
            'shippingMethods' => $shippingMethods,
            'returnShippingMethod' => $returnShippingMethod
        ]);
    }

    public function audits(Order $order) {
        return view('components._logs', ['audits' => $order->getAllAudits()]);
    }

    public function update(UpdateRequest $request, Order $order)
    {
        $updatedOrder = app('order')->update($request, $order);

        return response()->json([
            'success' => true,
            'message' => __('Order successfully updated.'),
            'order' => $updatedOrder
        ]);
    }

    public function destroy(DestroyRequest $request, Order $order)
    {
        app('order')->destroy($request, $order);

        return redirect()->route('order.index')->withStatus(__('Order successfully deleted.'));
    }

    public function filterCustomers(Request $request)
    {
        return app('order')->filterCustomers($request);
    }

    public function filterProducts(Request $request, Customer $customer = null)
    {
        return app('order')->filterProducts($request, $customer);
    }

    public function getOrderStatus(Request $request, Customer $customer)
    {
        return app('order')->getOrderStatus($request, $customer);
    }

    public function getBulkOrderStatus(Request $request)
    {
        return app('order')->getBulkOrderStatus($request);
    }

    public function webshipperShippingRates(Order $order)
    {
        return app('shipment')->webshipperShippingRates($order);
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function cancelOrder(Order $order): mixed
    {
        $canceledOrder = app('order')->cancelOrder($order);

        return redirect()->route('order.edit', ['order' => $canceledOrder->id])->withStatus(__('Order successfully canceled.'));
    }

    /**
     * @param Order $order
     * @param OrderItem $orderItem
     * @return mixed
     */
    public function cancelOrderItem(Order $order, OrderItem $orderItem): mixed
    {
        app('order')->cancelOrderItem($orderItem);

        return redirect()->route('order.edit', ['order' => $order->id])->withStatus(__('Order item successfully canceled.'));
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function fulfillOrder(Order $order): mixed
    {
        app()->order->markAsFulfilled($order);

        return redirect()->route('order.edit', ['order' => $order->id])->withStatus(__('Order marked as fulfilled.'));
    }

    public function getItem(Order $order): \Illuminate\Contracts\View\View
    {
        return View::make('shared.modals.components.orderDetails', compact('order'));
    }

    public function getKitItems(OrderItem $orderItem): \Illuminate\Contracts\View\View
    {
        return View::make('shared.modals.showDynamicKitItems', compact('orderItem'));
    }

    public function getOrderSlip(Order $order)
    {
        return app('order')->getOrderSlip($order);
    }

    /**
     * @param ImportCsvRequest $request
     * @return JsonResponse
     */
    public function importCsv(ImportCsvRequest $request): JsonResponse
    {
        $message = app('order')->importCsv($request);

        return response()->json([
            'success' => true,
            'message' => __($message)
        ]);
    }

    /**
     * @param ExportCsvRequest $request
     * @return mixed
     */
    public function exportCsv(ExportCsvRequest $request)
    {
        return app('order')->exportCsv($request);
    }

    public function reship(Order $order, ReShipRequest $request)
    {
        $this->authorize('reship', $order);

        $reshippedItemsNum = app('order')->reshipOrderItems($order, $request);

        if ($reshippedItemsNum > 0) {
            $status = __('Order item'.($reshippedItemsNum>1?'s':'').' successfully re-shipped.');
        } else {
            $status = __('Something went wrong');
        }

        return redirect()->route('order.edit', ['order' => $order->id])->withStatus($status);
    }

    /**
     * @param Order $order
     * @param StoreOrderReturnRequest $request
     * @return JsonResponse
     */
    public function return(Order $order, StoreOrderReturnRequest $request): JsonResponse
    {
        $return = app('return')->storeOrderReturn($order, $request);

        if (is_null($return)) {
            throw new HttpException(500, __('An error has occurred'));
        }

        if (!empty($return->returnLabels) && count($return->returnLabels) > 0) {

            $returnLabels = [];

            foreach ($return->returnLabels as $key => $returnLabel) {
                $returnLabels[] = [
                    'url' => route('return.label', [
                        'return' => $return,
                        'returnLabel' => $returnLabel
                    ]),
                    'name' => __('Label :number', ['number' => $key + 1])
                ];
            }

            if (count($returnLabels) > 0) {
                // Send email
                app('return')->sendReturnOrderWithLabelsMail($return, $returnLabels);

                return response()->json([
                    'success' => true,
                    'message' => __('Order successfully returned with labels. Email will be send with return label information')
                ]);
            }
        }

        if ($request->get('own_label') === '1') {
            return response()->json([
                'success' => true,
                'message' => __('Order successfully returned')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('An error has occurred')
        ]);
    }

    /**
     * @param BulkEditRequest $request
     * @return void
     * @throws AuthorizationException
     */
    public function bulkEdit(BulkEditRequest $request)
    {
        $this->authorize('update', Order::class);

        app('order')->bulkEdit($request);
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     * @throws AuthorizationException
     */
    public function bulkCancel(BulkSelectionRequest $request)
    {
        $this->authorize('update', Order::class);

        app('order')->bulkCancel($request);
    }

    /**
     * @param BulkSelectionRequest $request
     * @return void
     * @throws AuthorizationException
     */
    public function bulkFulfill(BulkSelectionRequest $request)
    {
        $this->authorize('update', Order::class);

        app('order')->bulkFulfill($request);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function countRecords(Request $request): JsonResponse
    {
        $filterInputs =  $request->get('filter_form');
        $search = $request->get('search');

        if ($term = Arr::get($search, 'value')) {
            $filterInputs = [];
        }

        $orderCollection = app('order')->getQuery($filterInputs)
            ->setEagerLoads([]);

        if ($term) {
            $orderCollection = app('order')->searchQuery($term, $orderCollection);
        }

        $results = __('Total records:') . ' ' . $orderCollection->getQuery()->getCountForPagination();

        return response()->json([
            'results' => $results
         ]);
    }
}
