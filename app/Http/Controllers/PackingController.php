<?php

namespace App\Http\Controllers;

use App\Components\OrderComponent;
use App\Components\PackingComponent;
use App\Exceptions\ShippingException;
use App\Http\Requests\Packing\BulkShipStoreRequest;
use App\Http\Requests\Packing\StoreRequest;
use App\Http\Resources\PackingSingleOrderShippingTableResource;
use App\Jobs\SyncBulkShipBatchOrders;
use App\Mail\BulkShipping\BatchShipped;
use App\Models\BulkShipBatch;
use App\Models\CustomerSetting;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PackageOrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\ShipmentLabel;
use App\Models\ShippingBox;
use App\Models\Tote;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\PdfReader\PageBoundaries;
use setasign\Fpdi\Tcpdf\Fpdi;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PackingController extends Controller
{
    protected PackingComponent $packing;

    public function __construct(PackingComponent $packing)
    {
        $this->packing = $packing;
        $this->authorizeResource(Order::class);
    }

    public function index()
    {
        $customerIds = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $shipments = Shipment::whereHas('order', function(Builder $query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            })
            ->with(['order', 'shippingMethod', 'shipmentTrackings', 'shipmentLabels'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('packing.index', [
            'shipments' => $shipments,
            'page' => 'packing_single_order_shipping',
            'datatableOrder' => app()->editColumn->getDatatableOrder('packing-single-order-shipping'),
        ]);
    }

    public function singleOrderShippingDataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'orders.ordered_at';
        $sortDirection = 'asc';
        $filterInputs = $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $orderQuery = Order::with('orderItems.product.productImages', 'customer.contactInformation')
            ->whereDoesntHave('orderItems.pickingBatchItems.pickingBatch.tasks', function ($query) {
                $query->whereNull('completed_at');
            })
            ->whereHas('orderItems', function ($query) {
                $query->where('quantity_allocated_pickable', '>', 0);
            })
            ->withCount('orderItems')
            ->where('ready_to_pick', 1)
            ->when($filterInputs, function ($query) use ($filterInputs) {
                // Ordered at
                if (Arr::get($filterInputs, 'ordered_at')) {
                    $query->where('ordered_at', '>=', Carbon::parse($filterInputs['ordered_at'])->toDateString());
                }

                // Required ship date
                if (Arr::get($filterInputs, 'shipping_date_before_at')) {
                    $query->where('shipping_date_before_at', Carbon::parse($filterInputs['shipping_date_before_at'])->toDateString());
                }
            })
            ->groupBy('orders.id')
            ->orderBy($sortColumnName, $sortDirection);

        $orderQuery = $orderQuery->whereIn('customer_id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            $term = $term . '%';
            $orderQuery = $orderQuery->where('number', 'like', $term);
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $orderQuery = $orderQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $orderQuery = $orderQuery->get();
        $orderCollection = PackingSingleOrderShippingTableResource::collection($orderQuery);

        return response()->json([
            'data' => $orderCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('packing-single-order-shipping'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function barcodeSearch($barcode): JsonResponse
    {
        $order = app('packing')->barcodeSearch($barcode);

        if ($order) {
            return response()->json([
                'success' => true,
                'redirect' => route('packing.single_order_shipping', ['order' => $order->id])
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }

    /**
     * @param Request $request
     * @param Order $order
     * @param BulkShipBatch|null $bulkShipBatch
     * @return Application|Factory|View
     * @throws AuthorizationException
     */
    public function singleOrderShipping(Request $request, Order $order, BulkShipBatch $bulkShipBatch = null)
    {
        $toteOrderItemArr = [];

        $this->authorize('singleOrderShipping', $order);

        $customer = $order->customer;

        $shippingBoxes = $customer->shippingBoxes;

        $shippingMethods = collect($customer->shippingMethods);

        if ($customer->parent_id) {
            $shippingBoxes = $shippingBoxes->merge($customer->parent->shippingBoxes);
            $shippingMethods = $shippingMethods->merge($customer->parent->shippingMethods);
        }

        $order = $order->load(
            'orderItems.product.productImages',
            'orderItems.product.locations.warehouse',
            'orderItems.placedToteOrderItems.location',
            'shippingContactInformation.country',
            'bulkShipBatch',
        );

        $printers = $customer->printers;

        if ($customer->parent_id) {
            $printers = $printers->merge($customer->parent->printers);
        }

        foreach ($order->orderItems as $key => $orderItem) {

            $toteOrderItemArr[$orderItem->id]['total_picked'] = 0;
            $toteOrderItemArr[$orderItem->id]['total_in_totes'] = 0;

            if ($orderItem->quantity_allocated > 0) {

                foreach ($orderItem->placedToteOrderItems as $toteOrderItem) {
                    $toteOrderItemArr[$orderItem->id]['total_in_totes'] += $toteOrderItem->quantity;
                }

                foreach ($orderItem->product->locations as $location) {
                    if (isset($orderItem->placedToteOrderItems)) {
                        foreach ($orderItem->placedToteOrderItems as $toteOrderItem) {

                            if ($toteOrderItem->location_id == $location->id) {

                                $toteLocationIndex = $location->id . '-' . $toteOrderItem->tote->id;

                                if (isset($toteOrderItemArr[$orderItem->id]['locations'][$toteLocationIndex])) {
                                    $toteOrderItemArr[$orderItem->id]['locations'][$toteLocationIndex]['tote_order_item_quantity'] += $toteOrderItem->quantity;
                                    $toteOrderItemArr[$orderItem->id]['locations'][$toteLocationIndex]['tote_name'] .= ', ' . $toteOrderItem->tote->name;
                                } else {
                                    $toteOrderItemArr[$orderItem->id]['locations'][$toteLocationIndex] = [
                                        'key' => $key,
                                        'order_item' => $orderItem,
                                        'tote_order_item' => $toteOrderItem,
                                        'tote_order_item_quantity' => $toteOrderItem->quantity,
                                        'tote_name' => $toteOrderItem->tote->name,
                                        'tote_id' => $toteOrderItem->tote->id,
                                    ];
                                }

                                $toteOrderItemArr[$orderItem->id]['total_picked'] += $toteOrderItem->quantity;
                            }
                        }
                    }
                }
            }
        }

        return view('packing.shipping', [
            'order' => $order,
            'bulkShipBatch' => $bulkShipBatch,
            'shippingBoxes' => $shippingBoxes,
            'shippingMethods' => collect($shippingMethods),
            'page' => 'packing_single_order_shipping',
            'printers' => $printers,
            'toteOrderItemArr' => $toteOrderItemArr
        ]);
    }

    /**
     * @param StoreRequest $request
     * @param Order $order
     * @param BulkShipBatch|null $bulkShipBatch
     * @return JsonResponse
     */
    public function singleOrderShip(StoreRequest $request, Order $order, BulkShipBatch $bulkShipBatch = null): JsonResponse
    {
        try {
            $shipment = app(PackingComponent::class)->packAndShip($order, $request);

            if ($shipment && count($shipment->shipmentLabels) > 0) {
                Session::flash('status', 'Shipment successfully created!');
                $shipmentLabels = [];

                $labelNumber = 1;

                foreach ($shipment->shipmentLabels as $shipmentLabel) {
                    if ($shipmentLabel->type === ShipmentLabel::TYPE_RETURN) {
                        $labelName = __('Return label');
                    } else {
                        $labelName = __('Label :number', ['number' => $labelNumber++]);
                    }

                    $shipmentLabels[] = [
                        'url' => route('shipment.label', [
                            'shipment' => $shipment,
                            'shipmentLabel' => $shipmentLabel
                        ]),
                        'name' => $labelName
                    ];
                }

                // Audit custom event
                $order->auditSingleOrderShipCustomEvent($shipment);

                if ($request->print_packing_slip) {
                    $shipmentLabels[] = [
                        'url' => route('shipment.getPackingSlip', [
                            'shipment' => $shipment,
                        ]),
                        'name' => __('Packing slip')
                    ];

                    if ($order->custom_invoice_url) {
                        $shipmentLabels[] = [
                            'url' => $order->custom_invoice_url,
                            'name' => __('Invoice')
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'labels' => $shipmentLabels
                ]);
            }
        } catch (ShippingException $exception) {
            throw new HttpException(500, $exception->getMessage());
        }

        throw new HttpException(500);
    }

    public function bulkShipBatchShipping(Request $request, BulkShipBatch $bulkShipBatch)
    {
        if ($order = $bulkShipBatch->orders()->wherePivot('shipped', false)->first()) {
            return $this->singleOrderShipping($request, $order, $bulkShipBatch);
        }

        return redirect()->route('bulk_shipping.batches');
    }

    public function bulkShipBatchShip(BulkShipStoreRequest $storeRequest, BulkShipBatch $bulkShipBatch)
    {
        $batchStatus = [];
        $tagName = 'BULK-' . $bulkShipBatch->id;

        $bulkShipBatch->update([
            'shipped' => true,
            'shipped_at' => now(),
        ]);

        foreach ($bulkShipBatch->orders as $order) {
            try {
                $request = $storeRequest->getOrderRequestInstance($order->id);

                if (!$request) {
                    throw new Exception('Not enough inventory');
                }

                $shipment = $this->packing->packAndShip($order, $request);

                $batchStatus[$order->id]['success'] = true;

                $order->tags()->firstOrCreate([
                    'customer_id' => $order->customer_id,
                    'name' => $tagName
                ]);

                $bulkShipBatch->orders()->sync([
                    $order->id => [
                        'shipped' => true,
                        'errors' => false,
                        'shipment_id' => $shipment->id
                    ]
                ], false);
            } catch (Exception $e) {
                $bulkShipBatch->orders()->detach($order);

                $newTotalOrders = $bulkShipBatch->orders->count();
                $itemsPerOrder = $bulkShipBatch->orders->first()->orderItems->count();

                $bulkShipBatch->update([
                    'total_orders' => $newTotalOrders,
                    'total_items' => $itemsPerOrder * $newTotalOrders,
                ]);

                $order->tags()->firstOrCreate([
                    'customer_id' => $order->customer_id,
                    'name' => 'FAILED-' . $tagName
                ]);
            }
        }

        if ($bulkShipBatch->orders()->get()->count() > 0) {
            Mail::to(auth()->user()->email)->queue(
                new BatchShipped($bulkShipBatch)
            );

            return response()->json([
                'success' => true,
                'batchStatus' => $batchStatus,
                'labels' => $this->getBulkShipPDF($bulkShipBatch),
            ]);
        }

        SyncBulkShipBatchOrders::dispatch();

        return response()->json([
            'success' => false,
            'batchStatus' => $batchStatus,
            'labels' => [],
        ]);
    }

    public function getBulkShipPDF(BulkShipBatch $bulkShipBatch): array
    {
        $bulkShipBatch = $bulkShipBatch->refresh();

        if (count($bulkShipBatch->orders) == 0) {
            return [];
        }

        $labelDirectory = 'public/bulk_ships/';
        $pdfName = sprintf("%d", $bulkShipBatch->id) . '_bulk_ship.pdf';

        if (!Storage::exists($labelDirectory)) {
            Storage::makeDirectory($labelDirectory);
        }

        $summaryPagePath = $labelDirectory . $pdfName;

        $outputLabels = [
            [
                'url' => Storage::url($summaryPagePath),
                'name' => 'Merged label'
            ]
        ];

        $labelWidth = dimension_width($bulkShipBatch->orders->first()->customer, 'label');
        $labelHeight = dimension_height($bulkShipBatch->orders->first()->customer, 'label');

        PDF::loadView('bulk_shipping.pdf', [
            'bulkShipBatch' => $bulkShipBatch,
            'barcodeRows' => $this->getBarcodeRows($bulkShipBatch),
        ])->setPaper([0, 0, $labelWidth, $labelHeight])
            ->save(Storage::path($summaryPagePath));

        $fpdi = new Fpdi('P', 'pt', array($labelWidth, $labelHeight));
        $fpdi->setPrintHeader(false);
        $fpdi->setPrintFooter(false);

        $this->addLabelToBulkLabel($fpdi, Storage::path($summaryPagePath));

        $labelIndex = 0;

        foreach ($bulkShipBatch->orders as $order) {
            $shipment = Shipment::find($order->pivot->shipment_id);

            if ($shipment) {
                if ($shipment->voided_at) {
                    $bulkShipBatch->orders()->sync([
                        $order->id => [
                            'labels_merged' => true
                        ]
                    ], false);
                } else {
                    foreach ($shipment->shipmentLabels ?? [] as $shipmentLabel) {
                        $shippingLabelContent = $shipmentLabel->content;

                        if (!$shippingLabelContent && $shipmentLabel->url) {
                            $shippingLabelContent = base64_encode(file_get_contents($shipmentLabel->url));
                        }

                        if ($shippingLabelContent) {
                            $labelPath = Storage::path($labelDirectory . "label_{$labelIndex}_{$pdfName}");
                            file_put_contents($labelPath, base64_decode($shippingLabelContent));

                            $labelIndex++;

                            if ($this->addLabelToBulkLabel($fpdi, $labelPath)) {
                                $bulkShipBatch->orders()->sync([
                                    $order->id => [
                                        'labels_merged' => true
                                    ]
                                ], false);
                            } else {
                                $outputLabels[] = [
                                    'url' => route('shipment.label', [
                                        'shipment' => $shipment,
                                        'shipmentLabel' => $shipmentLabel
                                    ]),
                                    'name' => 'Failed to merged for order ' . $order->number
                                ];
                            }
                        }
                    }
                }
            }
        }

        $this->addLabelToBulkLabel($fpdi, Storage::path($summaryPagePath));

        $fpdi->Output(Storage::path($summaryPagePath), 'F');

        $bulkShipBatch->update(['label' => $summaryPagePath]);

        return $outputLabels;
    }

    public function getBarcodeRows(BulkShipBatch $bulkShipBatch): array
    {
        $orderItems = OrderItem::whereIn('order_id', $bulkShipBatch->orders->modelKeys())->get();
        $packageItems = PackageOrderItem::whereIn('order_item_id', $orderItems->modelKeys())->get();
        $barcodeRows['ids'] = $packageItems->groupBy('location_id')
            ->map(function($locationPackageItems) {
                return $locationPackageItems->map(function($packageItem) {
                    return [
                        'product_id' => $packageItem->orderItem->product_id,
                        'quantity' => $packageItem->quantity,
                        'location_id' => $packageItem->location_id,
                    ];
                })->sortBy('product_id')
                    ->groupBy('product_id')
                    ->map(function($products) {
                        return $products->sum('quantity');
                    });
            })->toArray();

        $locations = array_keys($barcodeRows['ids']);
        $products = array_merge(...array_map(function($products) {
            return array_keys($products);
        }, $barcodeRows['ids']));

        $barcodeRows['names'] = [
            'locations' => Location::whereIn('id', $locations)->get(['name', 'barcode', 'id'])->keyBy('id')->toArray(),
            'products' => Product::whereIn('id', $products)->get(['name', 'barcode', 'id'])->keyBy('id')->toArray(),
        ];

        $barcodeRows['box'] = ShippingBox::where('id', $packageItems->first()->package->shipping_box_id)
            ->selectRaw('id, name, CONCAT(length, " x ", width, " x ", height) AS size')
            ->first()
            ->toArray();

        return $barcodeRows;
    }

    private function addLabelToBulkLabel(Fpdi $fpdi, $labelPath)
    {
        $success = true;

        $pageCount = $fpdi->setSourceFile($labelPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            try {
                $fpdi->AddPage();
                $tplId = $fpdi->importPage($i, PageBoundaries::ART_BOX);
                $size = $fpdi->getTemplateSize($tplId);
                $fpdi->useTemplate($tplId, $size);

            } catch (Exception $exception) {
                Log::error('[bulkship] Failed to merge label ' . $labelPath, [$exception->getMessage()]);
                $success = false;
            }
        }

        return $success;
    }
}
