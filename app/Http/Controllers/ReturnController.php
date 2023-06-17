<?php

namespace App\Http\Controllers;

use App\Http\Dto\Filters\ReturnsDataTableDto;
use App\Http\Requests\Return_\BulkEditRequest;
use App\Http\Requests\Return_\DestroyRequest;
use App\Http\Requests\Return_\ReceiveBatchRequest;
use App\Http\Requests\Return_\StoreRequest;
use App\Http\Requests\Return_\UpdateRequest;
use App\Http\Requests\Return_\UpdateStatusRequest;
use App\Http\Resources\ReturnTableResource;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Return_;
use App\Models\ReturnItem;
use App\Models\ReturnLabel;
use App\Models\ReturnStatus;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Return_::class);

        foreach ($this->middleware as $key => $value) {
            if (isset($value['middleware']) && str_contains($value['middleware'], ',return_')) {
                $this->middleware[$key]['middleware'] = str_replace(',return_', ',return', $value['middleware']);
            }
        }
    }

    public function index($keyword='')
    {
        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $data = new ReturnsDataTableDto(
            ReturnStatus::whereIn('customer_id', $customers)->get()->pluck('name', 'id'),
            Warehouse::whereIn('customer_id', $customers)->whereHas('contactInformation')->get(),
            Product::whereIn('customer_id', $customers)->get(),
        );

        return view('returns.index', [
            'page' => 'returns',
            'keyword'=> $keyword,
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('returns'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'returns.id';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customers = app()->user->getSelectedCustomers();
        $customerIds = $customers->pluck('id')->toArray();

        $filterCustomerId = Arr::get($filterInputs, 'customer_id');

        if ($filterCustomerId && $filterCustomerId != 'all') {
            $customerIds = array_intersect($customerIds, [$filterCustomerId]);
        }

        $returnOrdersCollection = Return_::query()
            ->join('orders', 'returns.order_id', '=', 'orders.id')
            ->with([
                'order.customer.contactInformation',
                'tags',
                'returnLabels',
                'returnTrackings'
            ])
            ->where(function($query) use ($filterInputs) {
                // Find by filter resuls
                // Start/End date
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('returns.created_at', [$startDate, $endDate]);
                }

                // Order Status
                if (Arr::get($filterInputs, 'return_status')) {
                    if ($filterInputs['return_status'] === 'pending') {
                        $query->whereNull('return_status_id');
                    } else {
                        $query->whereHas('returnStatus', function($q) use ($filterInputs) {
                            return $q->where('id', (int)$filterInputs['return_status']);
                        });
                    }
                }
                // SKU
                if (Arr::get($filterInputs, 'sku')) {
                    $orders = Order::query()->whereHas('orderItems', function ($q) use ($filterInputs) {
                       return  $q->where('sku', 'like', '%'.$filterInputs['sku'].'%');
                    })->pluck('id');

                    $query->whereIn('orders.id', $orders ?? []);
                }

                // Warehouse
                if (Arr::get($filterInputs, 'warehouse')) {
                    $query->where('returns.warehouse_id', $filterInputs['warehouse']);
                }

                // Tags
                if (Arr::exists($filterInputs, 'tags') && Arr::get($filterInputs, 'tags')) {
                    $filterTags = (array) $filterInputs['tags'];
                    $query->whereHas('tags', function($query) use ($filterTags) {
                        $query->whereIn('name', $filterTags);
                    });
                }
            })
            ->when($customerIds, function($query) use ($customerIds) {
                return $query->whereHas('order', function($q) use ($customerIds) {
                    $q->whereIn('customer_id', $customerIds);
                });
            })
            ->select('returns.*')
            ->orderBy($sortColumnName, $sortDirection);

        if ($request->get('search')['value']) {
            $term = $request->get('search')['value'] . '%';

            $returnOrdersCollection->where(function ($q) use ($term) {
                $q->orWhereHas('order', function($q) use ($term) {
                    $q->where('number', 'like', $term);
                })
                    ->orWhere('returns.number', 'like', $term)
                    ->orWhereHas('order.customer.contactInformation', function($query) use ($term) {
                        $query->where('name', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $returnOrdersCollection = $returnOrdersCollection
                ->skip($request->get('start'))
                ->limit($request->get('length'));
        }

        $orders = $returnOrdersCollection->get();

        return response()->json([
            'data' => ReturnTableResource::collection($orders->load('items.product')),
            'visibleFields' => app()->editColumn->getVisibleFields('returns'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    public function show(Return_ $return): string
    {
        return view('returns.show')
            ->with([
                'return' => $return->load([
                    'warehouse.contactInformation',
                    'items',
                    'tags'
                ])
            ])->render();
    }

    /**
     * @param Return_ $return
     * @param string $keyword
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Return_ $return, string $keyword = '')
    {
        return view('returns.edit', ['return' => $return, 'keyword' => $keyword]);
    }

    public function status(Return_ $return): string
    {
        return view('returns.show')
            ->with([
                'status' => true,
                'return' => $return->load([
                    'warehouse.contactInformation',
                    'items',
                ])
            ])->render();
    }

    public function statusUpdate(UpdateStatusRequest $request, Return_ $return): JsonResponse
    {
        app()->return->updateStatus($request, $return);

        return response()->json([
            'success' => true,
            'message' => __('Return status successfully updated.')
        ]);
    }

    public function create(Return_ $return, Order $order = null)
    {
        $currentOrder = $order;

        if (is_null($order)) {
            $currentOrder = Order::query()->first();
        } else {
            $orderItems = app()->return->createReturnFromOrder($order);
        }

        $warehouse = Warehouse::query()->first();

        $data = [
            'status' => true,
            'defaultOrder' => [
                'id' => $currentOrder->id,
                'text' => $currentOrder->number
            ],
            'defaultWarehouse' =>  [
                'id' => $warehouse->id,
                'text' => $warehouse->contactInformation->name . ', ' . $warehouse->contactInformation->email . ', ' . $warehouse->contactInformation->zip . ', ' . $warehouse->contactInformation->city
            ],
            'order' => $order,
            'orderItems' => $orderItems ?? null,
        ];


        return view('returns.create', $data);
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        app()->return->store($request);

        return redirect()->route('return.index');
    }

    public function update(UpdateRequest $request, Return_ $return)
    {
        $customer = app()->user->getSelectedCustomers();

        app()->return->update($request, $return);

        app()->return->updateStatus($request, $return);

        $record = [];
        $order_item = null;

        foreach ($request['items'] as $item) {
            if (!empty($item['destination_id']) && !empty($item['quantity_to_receive'])) {
                if (!isset($item['order_item_id'])) {
                    $order_item = ReturnItem::where('product_id', $item['product_id'])->where('return_id', $return->id)->first();
                }
                $record[] = [
                    'source_id' => $return->id,
                    'source_type' => Return_::class,
                    'destination_id' => $item['destination_id'],
                    'destination_type' => Location::class,
                    'user_id' => Auth::user()->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity_to_receive'],
                    'return_item_id' => $item['return_item_id'] ?? $order_item->id,
                    'quantity_received' => $item['quantity_to_receive'],
                    'location_id' => $item['destination_id'],
                    'customer_id' => $customer->first()->id
                ];
            }
        }

        app()->return->receiveBatch(
            ReceiveBatchRequest::make($record),
            $return
        );

        return redirect()->back()->withStatus(__('Return successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param Return_ $return
     * @return Response
     */

    public function destroy(DestroyRequest $request, Return_ $return): Response
    {
        app()->return->destroy($request, $return);

        return redirect()->route('return.index')->withStatus(__('Return successfully deleted.'));
    }

    public function filterOrders(Request $request)
    {
        return app()->return->filterOrders($request);
    }

    public function filterStatuses(Request $request)
    {
        return app()->return->filterStatuses($request);
    }

    public function filterOrderProducts(Request $request, $orderId)
    {
        return app()->return->filterOrderProducts($request, $orderId);
    }

    public function filterLocations(Request $request)
    {
        return app()->return->filterLocations($request);
    }

    /**
     * @param BulkEditRequest $request
     * @return mixed
     * @throws AuthorizationException
     */
    public function bulkEdit(BulkEditRequest $request)
    {
        $this->authorize('update', Return_::class);

        return app('return')->bulkEdit($request);
    }

    public function label(Return_ $return, ReturnLabel $returnLabel)
    {
        return app('return')->label($return, $returnLabel);
    }
}
