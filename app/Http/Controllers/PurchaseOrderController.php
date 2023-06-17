<?php

namespace App\Http\Controllers;

use App\Http\Dto\Filters\PurchaseOrdersDataTableDto;
use App\Http\Requests\PurchaseOrder\BulkEditRequest;
use App\Http\Requests\PurchaseOrder\DestroyRequest;
use App\Http\Requests\Csv\ExportCsvRequest;
use App\Http\Requests\Csv\ImportCsvRequest;
use App\Http\Requests\PurchaseOrder\ReceiveBatchRequest;
use App\Http\Requests\PurchaseOrder\ReceivePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\StoreRequest;
use App\Http\Requests\PurchaseOrder\UpdateRequest;
use App\Http\Requests\PurchaseOrderItem\RejectPurchaseOrderItemRequest;
use App\Http\Resources\PurchaseOrderTableResource;
use App\Models\Customer;
use App\Models\RejectedPurchaseOrderItem;
use App\Models\Warehouse;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Throwable;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PurchaseOrder::class);
        $this->middleware('3pl')->only(['reject', 'receivePurchaseOrder']);
    }

    public function index($keyword='')
    {
        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $data = new PurchaseOrdersDataTableDto(
            PurchaseOrderStatus::whereIn('customer_id', $customers)->get()->pluck('name', 'id'),
            Warehouse::whereIn('customer_id', $customers)->get()
        );

        $additionalActions = [
            'backorders',
            'products',
            'reorders',
        ];

        return view('purchase_orders.index', [
            'keyword' => $keyword,
            'data' => $data,
            'additionalActions' => $additionalActions,
            'datatableOrder' => app()->editColumn->getDatatableOrder('purchase_orders'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'purchase_orders.id';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']] ? $tableColumns[$columnOrder[0]['column']]['name'] : 'purchase_orders.id';
            $sortDirection = $columnOrder[0]['dir'];
        }

        $purchaseOrdersCollection = app('purchaseOrder')->getQuery($filterInputs, $sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        if ($term) {
            app('purchaseOrder')->searchQuery($term, $purchaseOrdersCollection);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $purchaseOrdersCollection = $purchaseOrdersCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $purchaseOrders = $purchaseOrdersCollection->get();
        $visibleFields = app('editColumn')->getVisibleFields('purchase_orders');

        return response()->json([
            'data' => PurchaseOrderTableResource::collection($purchaseOrders),
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);

    }

    public function create()
    {
        $purchaseOrderStatuses = PurchaseOrderStatus::all();
        $additionalActions = [
            'backorders',
            'products',
            'reorders',
        ];

        return view('purchase_orders.create', [
            'purchaseOrderStatuses' => $purchaseOrderStatuses,
            'additionalActions' => $additionalActions,
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        app('purchaseOrder')->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Purchase order successfully created.')
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrderStatuses = PurchaseOrderStatus::all();

        return view('purchase_orders.edit', ['purchaseOrder' => $purchaseOrder, 'purchaseOrderStatuses' => $purchaseOrderStatuses]);
    }

    public function update(UpdateRequest $request, PurchaseOrder $purchaseOrder): JsonResponse
    {
        $input = $request->validated();

        /** @var Customer $customer */
        $customer = Customer::find($input['customer_id']);

        app('purchaseOrder')->update(UpdateRequest::make($input), $purchaseOrder);

        $record = [];
        $purchase_order_item = null;

        foreach (Arr::get($input, 'purchase_order_items') as $item) {
            if (!empty($item['destination_id']) && !empty($item['quantity_to_receive'])) {
                if (!isset($item['purchase_order_item_id'])) {
                    $purchase_order_item = PurchaseOrderItem::where('product_id', $item['product_id'])->where('purchase_order_id', $purchaseOrder->id)->first();
                }
                $record[] = [
                    'purchase_order_item_id' => $item['purchase_order_item_id'] ?? $purchase_order_item->id,
                    'quantity_received' => $item['quantity_to_receive'],
                    'location_id' => $item['destination_id'],
                    'customer_id' => $customer->id
                ];
            }
        }

        app('purchaseOrder')->receiveBatch(
            ReceiveBatchRequest::make($record),
            $purchaseOrder
        );

        return response()->json([
            'success' => true,
            'message' => __('Purchase Order successfully updated.')
        ]);
    }

    public function destroy(DestroyRequest $request, PurchaseOrder $purchaseOrder)
    {
        app('purchaseOrder')->destroy($request, $purchaseOrder);

        return response()->json([
            'success' => true,
            'message' => __('Purchase order successfully deleted')
        ]);
    }

    public function close(PurchaseOrder $purchaseOrder): JsonResponse
    {
        app('purchaseOrder')->closePurchaseOrder($purchaseOrder);

        return response()->json([
            'success' => true,
            'message' => __('Purchase order successfully closed')
        ]);
    }

    public function filterCustomers(Request $request)
    {
        return app('purchaseOrder')->filterCustomers($request);
    }

    public function filterWarehouses(Request $request, Customer $customer = null)
    {
        return app('purchaseOrder')->filterWarehouses($request, $customer);
    }

    public function filterSuppliers(Request $request, Customer $customer = null)
    {
        return app('purchaseOrder')->filterSuppliers($request, $customer);
    }

    public function filterProducts(Request $request)
    {
        return app('purchaseOrder')->filterProducts($request);
    }

    public function filterLocations(Request $request)
    {
        return app('purchaseOrder')->filterLocations($request);
    }

    public function getOrderStatus(Request $request, Customer $customer)
    {
        return app('purchaseOrder')->getOrderStatus($request, $customer);
    }

    public function receivePurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        if (is_null($purchaseOrder->closed_at)) {
            $purchaseOrder = $purchaseOrder->load(['warehouse.locations'=>function($query){
                $query->where('protected', true);
            }]);

            return view('purchase_orders.receive', compact('purchaseOrder'));
        }

        return redirect()->back()->withStatus([
            'type' => 'error',
            'message' => __('Purchase Order is closed and can\'t be received.'),
        ]);
    }

    public function updatePurchaseOrder(ReceivePurchaseOrderRequest $request, PurchaseOrder $purchaseOrder)
    {
        try {
            app()->purchaseOrder->updatePurchaseOrder($request, $purchaseOrder);
            return redirect()->back()->withStatus(__('Purchase Order successfully updated'));
        } catch (Throwable $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function reject(RejectPurchaseOrderItemRequest $request, PurchaseOrderItem $purchaseOrderItem): JsonResponse
    {
        app()->purchaseOrder->rejectPurchaseOrderItem($request, $purchaseOrderItem);

        return response()->json([
            'success' => true,
            'message' => __('Purchase order item successfully rejected')
        ]);
    }

    public function getPurchaseOrderModal(PurchaseOrder $purchaseOrder): \Illuminate\Contracts\View\View
    {
        $customer = app('user')->getSelectedCustomers();

        return View::make('shared.modals.components.purchase_orders.edit', compact('purchaseOrder', 'customer'));
    }

    public function getRejectedPurchaseOrderItemModal(PurchaseOrderItem $purchaseOrderItem): \Illuminate\Contracts\View\View
    {
        $rejectedItems = RejectedPurchaseOrderItem::wherePurchaseOrderItemId($purchaseOrderItem->id)->get();

        return View::make('shared.modals.components.purchase_orders.reject', compact('purchaseOrderItem', 'rejectedItems'));
    }

    /**
     * @param ImportCsvRequest $request
     * @return JsonResponse
     */
    public function importCsv(ImportCsvRequest $request): JsonResponse
    {
        $message = app('purchaseOrder')->importCsv($request);

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
        return app('purchaseOrder')->exportCsv($request);
    }

    /**
     * @param BulkEditRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function bulkEdit(BulkEditRequest $request)
    {
        $this->authorize('update', PurchaseOrder::class);

        return app('purchaseOrder')->bulkEdit($request);
    }
}
