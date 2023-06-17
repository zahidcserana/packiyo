<?php

namespace App\Components;

use App\Http\Requests\Shipment\ShipItemRequest;
use App\Http\Requests\Shipment\ShipRequest;
use App\Jobs\Webshipper\ProcessShipment;
use App\Models\Currency;
use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\ShipmentLabel;
use App\Models\Tote;
use App\Models\CustomerSetting;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class ShipmentComponent extends BaseComponent
{
    public function shipItem(ShipItemRequest $request, OrderItem $orderItem, Shipment $shipment)
    {
        $input = $request->validated();
        $quantityShipped = Arr::get($input, 'quantity');

        $shipmentItem = ShipmentItem::firstOrCreate([
            'shipment_id' => $shipment->id,
            'order_item_id' => $orderItem->id,
        ]);

        $shipmentItem->increment('quantity', $quantityShipped);

        $location = Location::find($input['location_id']);
        $tote = Tote::find($input['tote_id']);

        if ($tote) {
            $quantityRemainingInTote = $quantityShipped;

            $toteOrderItems = $tote->placedToteOrderItems()
                ->where('order_item_id', $orderItem->id)
                ->where('location_id', $location->id)
                ->get();

            foreach ($toteOrderItems as $toteOrderItem) {
                $quantityRemoveFromTote = min($quantityRemainingInTote, $toteOrderItem->quantity_remaining);

                $toteOrderItem->update([
                    'quantity_removed' => DB::raw('`quantity_removed` + ' . $quantityRemoveFromTote),
                    'quantity_remaining' => DB::raw('`quantity_remaining` - ' . $quantityRemoveFromTote),
                ]);

                $quantityRemainingInTote -= $quantityRemoveFromTote;
            }
        }

        $orderItem = $orderItem->refresh();
        $orderItem->quantity_shipped += $quantityShipped;
        $orderItem->save();

        if ($orderItem->parentOrderItem) {
            $kitItemQuantity = $orderItem->parentOrderItem->product->kitItems()->where('child_product_id', $orderItem->product_id)->first()->pivot->quantity ?? 1;
            $kitShippedQuantity = ceil($orderItem->quantity_shipped / $kitItemQuantity);

            $parentOrderItem = $orderItem->parentOrderItem->refresh();

            foreach ($parentOrderItem->kitOrderItems as $kitOrderItem) {
                $kitItemQuantity = $parentOrderItem->product->kitItems()->where('child_product_id', $kitOrderItem->product_id)->first()->pivot->quantity ?? 1;
                $kitShippedQuantity = min($kitShippedQuantity, ceil($kitOrderItem->quantity_shipped / $kitItemQuantity));
            }

            if ($kitShippedQuantity > $parentOrderItem->quantity_shipped) {
                $shipmentItemParent = ShipmentItem::firstOrCreate([
                    'shipment_id' => $shipment->id,
                    'order_item_id' => $orderItem->order_item_kit_id,
                ]);

                $shipmentItemParent->quantity = $kitShippedQuantity - ShipmentItem::where('id', '!=', $shipmentItemParent->id)->where('order_item_id', $orderItem->order_item_kit_id)->sum('quantity');
                $shipmentItemParent->save();

                $orderItem->parentOrderItem->quantity_shipped = $kitShippedQuantity;
                $orderItem->parentOrderItem->save();
            }
        }

        app('inventoryLog')->adjustInventory(
            $location, $orderItem->product, -$quantityShipped,InventoryLogComponent::OPERATION_TYPE_SHIP, $shipment
        );

        return $shipmentItem;
    }

    public function filterOrders(Request $request)
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $orders = Order::where('id', '=', $term)->get();

            if ($orders->count() == 0) {
                $orders= Order::where('number', 'like', $term . '%')->get(['id', 'number']);
            }

            foreach ($orders as $order) {
                if ($order->count()) {
                    $results[] = [
                        'id' => $order->id,
                        'text' => $order->number
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterOrderProducts(Request $request, $orderId): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $orderItems = OrderItem::where('order_id', $orderId)->where('id', $term)->get();

            if ($orderItems->count() == 0) {
                $orderItems = OrderItem::where('order_id', '=', $orderId)->whereHas('product', function ($query) use ($term) {
                    $term = $term . '%';

                    $query->where('name', 'like', $term);
                    $query->orWhere('sku', 'like', $term);
                })->get();
            }

            foreach ($orderItems as $orderItem) {
                if ($orderItem->count()) {
                    $results[] = [
                        'id' => $orderItem->id,
                        'text' => 'SKU: ' . $orderItem->product->sku . ', NAME:' . $orderItem->product->name
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterOrderProductLocation(Request $request, $orderItemId)
    {
        $productId = OrderItem::where('id', $orderItemId)->first()->product_id;
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $locationProducts = LocationProduct::where('product_id', $productId)
                ->whereHas('location', function($query) use ($term) {
                    $term = $term . '%';

                    $query->where('name', 'like', $term);
                })
                ->get();

            foreach ($locationProducts as $locationProduct) {
                if ($locationProduct->location->count()) {
                    $results[] = [
                        'id' => $locationProduct->location->id,
                        'text' => $locationProduct->location->name
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * @param Shipment $shipment
     * @param ShipmentLabel $shipmentLabel
     * @return Application|ResponseFactory|RedirectResponse|Response|Redirector
     */
    public function label(Shipment $shipment, ShipmentLabel $shipmentLabel) {
        if ($shipmentLabel->shipment_id !== $shipment->id) {
            abort('403');
        }

        if ($shipmentLabel->content) {
            return response(base64_decode($shipmentLabel->content))->header('Content-Type', 'application/pdf');
        }

        if ($shipmentLabel->url) {
            return redirect($shipmentLabel->url);
        }

        abort(404);
    }

    public function getPackingSlip(Shipment $shipment)
    {
        $locale = customer_settings($shipment->order->customer_id, CustomerSetting::CUSTOMER_SETTING_LOCALE);
        if ($locale) {
            app()->setLocale($locale);
        }

        $this->generatePackingSlip($shipment);

        return response()->file(Storage::path($shipment->packing_slip), [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function generatePackingSlip(Shipment $shipment)
    {
        $shipment->refresh();
        $shipment = $shipment->load('packages.shipment.shipmentItems.orderItem');

        $path = 'public/packing_slips';
        $pdfName = sprintf("%011d", $shipment->id) . '_packing_slip.pdf';

        if (! Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        $path .= '/' . $pdfName;

        $paperWidth = dimension_width($shipment->order->customer, 'document');
        $paperHeight = dimension_height($shipment->order->customer, 'document');

        PDF::loadView('packing_slip.document', [
            'shipment' => $shipment,
            'showPricesOnSlip' => customer_settings($shipment->order->customer->id, CustomerSetting::CUSTOMER_SETTING_SHOW_PRICES_ON_SLIPS),
            'currency' => $shipment->order->currency->symbol ?? Currency::find(customer_settings($shipment->order->customer->id, CustomerSetting::CUSTOMER_SETTING_CURRENCY))->symbol ?? ''
        ])
        ->setPaper([0, 0, $paperWidth, $paperHeight])
        ->save(Storage::path($path));

        $shipment->update(['packing_slip' => $path]);
    }
}
