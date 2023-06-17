<?php

namespace App\Components;

use App\Http\Requests\Return_\BulkEditRequest;
use App\Http\Requests\Return_\DestroyBatchRequest;
use App\Http\Requests\Return_\DestroyRequest;
use App\Http\Requests\Return_\FilterRequest;
use App\Http\Requests\Return_\ReceiveBatchRequest;
use App\Http\Requests\Return_\ReceiveRequest;
use App\Http\Requests\Return_\StoreBatchRequest;
use App\Http\Requests\Return_\StoreRequest;
use App\Http\Requests\Return_\UpdateBatchRequest;
use App\Http\Requests\Return_\UpdateRequest;
use App\Http\Requests\Return_\UpdateStatusRequest;
use App\Http\Resources\ReturnCollection;
use App\Http\Resources\ReturnResource;
use App\Jobs\Order\RecalculateReadyToShipOrders;
use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Return_;
use App\Models\ReturnItem;
use App\Models\ReturnStatus;
use App\Models\Webhook;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Http\Requests\Order\StoreReturnRequest as StoreOrderReturnRequest;
use App\Mail\ReturnOrderWithLabelsMail;
use App\Models\PrintJob;
use App\Models\ReturnLabel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


class ReturnComponent extends BaseComponent
{
    public const NUMBER_PREFIX = 'RET-';

    /**
     * @param StoreRequest $request
     * @param $fireWebhook
     * @return Return_|Model
     */
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $input['number'] = Return_::getUniqueIdentifier(self::NUMBER_PREFIX, $input['warehouse_id']);

        if (isset($input['return_status_id']) && $input['return_status_id'] === 'pending') {
            Arr::forget($input, 'return_status_id');
        }

        $return = Return_::create(Arr::except($input, ['items']));

        if (isset($input['items'])) {
            $this->updateReturnItems($return, $input['items']);
        }

        $tags = Arr::get($input, 'tags');

        if (!empty($tags)) {
            $this->updateTags($tags, $return);
        }

        if ($fireWebhook) {
            $this->webhook(new ReturnResource($return), Return_::class, Webhook::OPERATION_TYPE_STORE, $return->order->customer_id);
        }

        $return->order->auditOrderCustomEvent('return', $return->number);

        return $return;
    }

    public function storeBatch(StoreBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Return_::class, ReturnCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    /**
     * @param UpdateRequest $request
     * @param Return_ $return
     * @param $fireWebhook
     * @return Return_
     */
    public function update(UpdateRequest $request, Return_ $return, $fireWebhook = true)
    {
        $input = $request->validated();

        if (isset($input['return_status_id']) && $input['return_status_id'] === 'pending') {
            $input['return_status_id'] = null;
        }

        if (isset($input['items'])) {
            $this->updateReturnItems($return, Arr::get($input, 'items'));
        }

        Arr::forget($input, 'items');
        Arr::forget($input, 'order_id');

        if (Arr::exists($input, 'tags')) {
            $this->updateTags(Arr::get($input, 'tags'), $return, true);
        }

        $return->update($input);

        if ($fireWebhook) {
            $this->webhook(new ReturnResource($return), Return_::class, Webhook::OPERATION_TYPE_UPDATE, $return->order->customer_id);
        }

        return $return;
    }

    public function updateBatch(UpdateBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $return = Return_::where('number', $record['number'])->first();

            $responseCollection->add($this->update($updateRequest, $return, false));
        }

        $this->batchWebhook($responseCollection, Return_::class, ReturnCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    public function updateStatus(UpdateStatusRequest|UpdateRequest $request, Return_ $return)
    {
        if (isset($request['return_status_id']) && $request['return_status_id'] !== 'pending') {
            $return->update(['return_status_id' => $request->get('return_status_id')]);
        }
    }

    public function destroy(DestroyRequest $request, Return_ $return, $fireWebhook = true)
    {
        $return->returnItems()->delete();

        $return->delete();

        $response = ['id' => $return->id, 'customer_id' => $return->order->customer_id];

        if ($fireWebhook) {
            $this->webhook($response, Return_::class, Webhook::OPERATION_TYPE_DESTROY, $return->order->customer_id);
        }

        return $response;
    }

    public function destroyBatch(DestroyBatchRequest $request)
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $return = Return_::find($record['id']);

            $responseCollection->add($this->destroy($destroyRequest, $return, false));
        }

        $this->batchWebhook($responseCollection, Return_::class, ResourceCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function receive(ReceiveRequest $request, Return_ $return, ReturnItem $returnItem): ReturnItem
    {
        $input = $request->validated();

        $location = Location::where('id', $input['location_id'])->first();

        app('inventoryLog')->adjustInventory($location, $returnItem->product, $input['quantity_received'], InventoryLogComponent::OPERATION_TYPE_RECEIVE, $return);

        $returnItem->quantity_received += $input['quantity_received'];
        $returnItem->save();

        dispatch(new RecalculateReadyToShipOrders([$return->order_id]));

        return $returnItem;
    }

    public function receiveBatch(ReceiveBatchRequest $request, Return_ $return): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $receiveRequest = ReceiveRequest::make($record);
            $returnItem = ReturnItem::find($record['return_item_id']);

            $responseCollection->add($this->receive($receiveRequest, $return, $returnItem));
        }

        return $responseCollection;
    }

    public function filterOrders(Request $request): JsonResponse
    {
        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $term = $request->get('term');

        $orders = Order::whereIn('customer_id', $customers)
            ->where('number', $term)
            ->whereHas('shipments')
            ->get(['id', 'number'])
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'text' => $order->number,
                ];
            });

        return response()->json([
            'results' => $orders->toArray()
        ]);
    }

    public function filterStatuses(Request $request): JsonResponse
    {
        $term = $request->get('term');

        $results[] = [
            'id' => 'pending',
            'text' => 'Pending'
        ];

        $returnStatuses = ReturnStatus::where('id', $term)
            ->orWhere('name', 'LIKE', $term . '%');

        $returnStatuses = $returnStatuses->get(['id', 'name'])
            ->map(function ($returnStatus) {
                return [
                    'id' => $returnStatus->id,
                    'text' => $returnStatus->name,
                ];
            });

        $results = array_merge($results, $returnStatuses->toArray());

        return response()->json(compact('results'));
    }

    public function filterOrderProducts(Request $request, $orderId): JsonResponse
    {
        $term = $request->get('term');

        $orderItems = OrderItem::query();

        if ($term) {
            $orderItems = $orderItems->where('product_id', '=', $term);
        }

        $orderItems = $orderItems->when($orderId, function (Builder $query) use ($orderId) {
                $query->where('order_id', $orderId);
            })
            ->get();

        if ($orderItems->count() === 0) {
            $orderItems = OrderItem::where('order_id', $orderId)
                ->where('quantity_shipped', '>', 0)
                ->whereHas('product', function ($query) use ($term) {
                    $term = $term . '%';

                    $query->where('name', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                })->get();
        }

        if ($orderId) {
            $orderReturnsIds = Order::find($orderId)
                ->returns()
                // TODO Check if condition should be added
                // ->where('approved', true)
                ->pluck('id')
                ->toArray();
            $returnItems = ReturnItem::whereIn('return_id', $orderReturnsIds)
                ->whereIn('product_id', $orderItems->pluck('product_id')->toArray())
                ->get()
                ->groupBy('product_id')
                ->map(function ($items) {
                    return $items->sum('quantity');
                })
                ->toArray();
        }

        $results = [];

        foreach ($orderItems as $orderItem) {
            $quantity = $orderItem->quantity_shipped;

            if (isset($returnItems[$orderItem->product_id])) {
                $quantity -= $returnItems[$orderItem->product_id];
            }

            if ((int)$quantity > 0) {
                $results[] = [
                    'id' => $orderItem->product_id,
                    'image' => $orderItem->product->productImages->first()->source ?? asset('img/inventory.svg'),
                    'text' => 'NAME: ' . $orderItem->product->name . '<br>SKU: ' . $orderItem->product->sku,
                    'quantity' => $quantity,
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterLocations(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $term = $term . '%';

            $locations = Location::where('name', 'like', $term)->get();

            foreach ($locations as $location) {
                $results[] = [
                    'id' => $location->id,
                    'text' => $location->name
                ];
            }

            return response()->json([
                'results' => $results
            ]);
        }

        return response()->json([
            'results' => []
        ]);
    }

    public function filter(FilterRequest $request, $orderIds)
    {
        $query = Return_::query();

        $query->when($request['from_date_created'], function ($q) use($request){
            return $q->where('created_at', '>=', $request['from_date_created']);
        });

        $query->when($request['to_date_created'], function ($q) use($request){
            return $q->where('created_at', '<=', $request['to_date_created'].' 23:59:59');
        });

        $query->when($request['from_date_updated'], function ($q) use($request){
            return $q->where('updated_at', '>=', $request['from_date_updated']);
        });

        $query->when($request['to_date_updated'], function ($q) use($request){
            return $q->where('updated_at', '<=', $request['to_date_updated'].' 23:59:59');
        });

        $query->when(count($orderIds) > 0, function ($q) use($orderIds){
            return $q->whereIn('order_id', $orderIds);
        });

        return $query->paginate();
    }

    public function createReturnFromOrder(Order $order): Collection
    {
        $orderItems = [];

        foreach ($order->orderItems as $key => $item) {
            if ($item->quantity_shipped > 0) {
                $returns = ReturnItem::where('product_id', $item->product_id)->get();
                $quantity_returned = collect($returns)->sum('quantity_received');

                $orderItems[$key] = $item;
                $orderItems[$key]['quantity_returned'] = $quantity_returned;
            }
        }

        return new Collection($orderItems);
    }

    /**
     * @param BulkEditRequest $request
     * @return void
     */
    public function bulkEdit(BulkEditRequest $request): void
    {
        $input = $request->validated();

        if (Arr::exists($input, 'tags')) {
            $this->bulkUpdateTags(Arr::get($input, 'tags'), Arr::get($input, 'ids'), Return_::class);
        }
    }

    public function storeOrderReturn(Order $order, StoreOrderReturnRequest $request, $fireWebhook = true)
    {
        $input = $request->all();

        $return = app(ShippingComponent::class)->return($order, $request);

        if ($return) {
            $printerId = Arr::get($input, 'printer_id');
            if ($printerId) {
                foreach ($return->returnLabels as $returnLabel) {
                    PrintJob::create([
                        'object_type' => ReturnLabel::class,
                        'object_id' => $returnLabel->id,
                        'url' => route('return.label', [
                            'return' => $return,
                            'returnLabel' => $returnLabel,
                        ]),
                        'type' => $return->type,
                        'printer_id' => $printerId,
                        'user_id' => auth()->user()->id,
                    ]);
                }
            }

            if ($fireWebhook) {
                $this->webhook(new ReturnResource($return), Return_::class, Webhook::OPERATION_TYPE_STORE, $order->customer_id);
            }

            return $return;
        }

        return null;
    }

    /**
     * @param Return_ $return
     * @param array $returnLabels
     * @return true
     */
    public function sendReturnOrderWithLabelsMail(Return_ $return, array $returnLabels)
    {
        $subject = __('Return labels');
        $cc = config('mail.cc_mail');
        $bcc = config('mail.bcc_mail');

        if (config('app.env') === 'production') {
            $email = $return->order->customer->contactInformation->email;
        } else {
            $email = auth()->user()->email;
        }

        try {
            Mail::to($email)
                ->cc($cc)
                ->bcc($bcc)
                ->send(new ReturnOrderWithLabelsMail($subject, $return, $returnLabels));
        } catch(\Exception $exception) {
            Log::error($exception->getMessage());
        }

        return true;
    }

    /**
     * @param Return_ $return
     * @param $items
     * @return void
     */
    public function updateReturnItems(Return_ $return, $items)
    {
        $items = array_filter($items, static function ($item) {
            return isset($item['is_returned']);
        });

        $orderItems = $return->order
            ->orderItems()
            ->whereIn('product_id', array_keys($items))
            ->get()
            ->keyBy('product_id');

        $returnItems = [];
        foreach ($items as $key => $item) {
            if ((int) $item['quantity'] !== 0) {
                $orderItems[$key]->update([
                    'quantity_returned' => $item['quantity'],
                ]);

                $returnItems[$key] = ['quantity' => $item['quantity']];
            }
        }

        $return->returnItems()->sync($returnItems);
    }

    /**
     * @param Return_ $return
     * @param ReturnLabel $returnLabel
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|RedirectResponse|Response|Redirector|\Symfony\Component\HttpFoundation\Response|void
     */
    public function label(Return_ $return, ReturnLabel $returnLabel)
    {
        if ($returnLabel->return_id !== $return->id) {
            abort('403');
        }

        if ($returnLabel->content) {
            return response(base64_decode($returnLabel->content))->header('Content-Type', 'application/pdf');
        }
        if ($returnLabel->url) {
            return redirect($returnLabel->url);
        }

        abort(404);
    }
}
