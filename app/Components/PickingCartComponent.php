<?php

namespace App\Components;

use App\Http\Requests\PickingCart\{DestroyBatchRequest,
    DestroyRequest,
    StoreBatchRequest,
    StoreRequest,
    UpdateBatchRequest,
    UpdateRequest};
use App\Http\Resources\PickingCartCollection;
use App\Http\Resources\PickingCartResource;
use App\Models\PickingBatch;
use App\Models\PickingBatchItem;
use App\Models\PickingCart;
use App\Models\Product;
use App\Models\Tote;
use App\Models\ToteLock;
use App\Models\ToteOrderItem;
use App\Models\Warehouse;
use App\Models\Webhook;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PickingCartComponent extends BaseComponent
{
    public function store(StoreRequest $request, $fireWebhook = true)
    {
        $input = $request->validated();

        $cart = PickingCart::create($input);

        $cart->save();

        return $cart;
    }

    public function storeBatch(StoreBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $storeRequest = StoreRequest::make($record);
            $responseCollection->add($this->store($storeRequest, false));
        }

        $this->batchWebhook($responseCollection, Tote::class, PickingCartCollection::class, Webhook::OPERATION_TYPE_STORE);

        return $responseCollection;
    }

    public function update(UpdateRequest $request, PickingCart $cart, $fireWebhook = true): PickingCart
    {
        $input = $request->validated();

        $cart->update($input);

        if ($fireWebhook) {
            $this->webhook(new PickingCartResource($cart), Tote::class, Webhook::OPERATION_TYPE_UPDATE, $cart->warehouse->id);
        }

        return $cart;
    }

    public function updateBatch(UpdateBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $updateRequest = UpdateRequest::make($record);
            $cart = PickingCart::where('barcode', $record['barcode'])->first();

            $responseCollection->add($this->update($updateRequest, $cart, false));
        }

        $this->batchWebhook($responseCollection, PickingCart::class, PickingCartCollection::class, Webhook::OPERATION_TYPE_UPDATE);

        return $responseCollection;
    }

    /**
     * @throws Exception
     */
    public function destroy(DestroyRequest $request): bool
    {
        $input = $request->validated();

        $cart = PickingCart::whereId($input['id'])->firstOrFail();

        try {
            $cart->delete();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function destroyBatch(DestroyBatchRequest $request): Collection
    {
        $responseCollection = new Collection();

        $input = $request->validated();

        foreach ($input as $record) {
            $destroyRequest = DestroyRequest::make($record);
            $tote = PickingCart::find($record['id']);

            $responseCollection->add($this->destroy($tote));
        }

        $this->batchWebhook($responseCollection, PickingCart::class, PickingCartCollection::class, Webhook::OPERATION_TYPE_DESTROY);

        return $responseCollection;
    }

    public function totes(PickingCart $pickingCart)
    {
        return $pickingCart->totes;
    }

    public function barcode(PickingCart $cart)
    {
        $generator = new BarcodeGeneratorPNG();

        $data = [
            'name' => $cart->name,
            'barcode' => $generator->getBarcode($cart->barcode, $generator::TYPE_CODE_128),
            'barcodeNumber' => $cart->barcode
        ];

        $paperWidth = dimension_width($cart->warehouse->customer, 'barcode');
        $paperHeight = dimension_height($cart->warehouse->customer, 'barcode');

        return PDF::loadView('pdf.barcode', $data)
            ->setPaper([0, 0, $paperWidth, $paperHeight])
            ->download('picking_cart_barcode.pdf');
    }

    public function filterWarehouses(Request $request): JsonResponse
    {
        $customer = app()->user->getSelectedCustomers();

        $term = $request->get('term');

        $results = [];

        $warehouses = Warehouse::whereHas('contactInformation', static function ($query) use ($term) {
                $term = $term . '%';

                $query->where('name', 'like', $term)
                    ->orWhere('company_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('zip', 'like', $term)
                    ->orWhere('city', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            });

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();

            $warehouses = $warehouses->whereIn('customer_id', $customers);
        }

        foreach ($warehouses->get() as $warehouse) {
            if ($warehouse->count()) {
                $results[] = [
                    'id' => $warehouse->id,
                    'text' => $warehouse->contactInformation->name . ', ' . $warehouse->contactInformation->email . ', ' . $warehouse->contactInformation->zip . ', ' . $warehouse->contactInformation->city
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function addItemToPickingCart(PickingBatch $pickingBatch, Product $product)
    {
        try {
            $pickingCart = PickingCart::whereId($pickingBatch->picking_cart_id)->first();

            $pickingBatchItem = PickingBatchItem::where('picking_batch_id', $pickingBatch->id)
                ->whereHas('orderItem', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->first();

            foreach ($pickingCart->totes as $tote) {
                if (is_null($tote->order_id)) {
                    ToteOrderItem::create([
                        'tote_id' => $tote->id,
                        'order_item_id' => $pickingBatchItem->order_item_id,
                        'picked_at' => Carbon::now()
                    ]);

                    $tote->order_id = $pickingBatchItem->orderItem->order_id;
                    $tote->save();

                    ToteLock::firstOrCreate([
                        'tote_id' => $tote->id,
                        'order_id' => $tote->order_id,
                        'lock_type' => ToteLock::LOCK_TYPE_PICKING
                    ]);

                    return $pickingCart;
                }
            }

            return Collection::empty();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return Collection::empty();
        }
    }
}
