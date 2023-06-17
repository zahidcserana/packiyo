<?php

namespace Database\Seeders;

use App\Models\InventoryLog;
use App\Models\Location;
use App\Models\PurchaseOrder;
use App\Models\Return_;
use App\Models\Shipment;
use Illuminate\Database\Seeder;

class InventoryLogPopulateNewData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventory_logs = InventoryLog::whereNull('location_id')->get();

        // First: populate location_id and associated_object in inventory log using source and destination data
        foreach($inventory_logs as $inventory_log)
        {
            $this->updateInventoryLogLocationAndAssociatedObject($inventory_log);
        }

        // Second: Populate new/previous on hand on updated inventory logs
        foreach($inventory_logs as $inventory_log)
        {
            $this->updateInventoryLogNewPreviousOnHand($inventory_log);
        }
    }

    protected function updateInventoryLogLocationAndAssociatedObject($inventory_log) {
        if($inventory_log->source_type === Location::class && $inventory_log->destination_type === Location::class) {
            $this->updateLocationAssociatedObject($inventory_log, $inventory_log->source->id, $inventory_log->destination_id, $inventory_log->destination_type);
        } else if ($inventory_log->source_type === Location::class && $inventory_log->destination_type === Shipment::class) {
            $this->updateLocationAssociatedObject($inventory_log, $inventory_log->source->id, $inventory_log->destination_id, $inventory_log->destination_type);
        } else if($inventory_log->source_type === PurchaseOrder::class && $inventory_log->destination_type === Location::class) {
            $this->updateLocationAssociatedObject($inventory_log, $inventory_log->destination->id, $inventory_log->source_id, $inventory_log->source_type);
        } else if ($inventory_log->source_type === Return_::class && $inventory_log->destination_type === Location::class) {
            $this->updateLocationAssociatedObject($inventory_log, $inventory_log->destination->id, $inventory_log->source_id, $inventory_log->source_type);
        }
    }

    protected function updateInventoryLogNewPreviousOnHand($inventory_log)
    {
        $product_id = $inventory_log->product_id;
        $new_on_hand = 0;
        $previous_on_hand = 0;

        $location_product = $inventory_log->location->products->filter(function($product) use ($product_id){
            return $product->product_id === $product_id;
        })->first();

        if($inventory_log->associated_object_type === Location::class && $inventory_log->associated_object_id == $inventory_log->location_id) {
            // Manually adjusted
            $associated_object_location_product = $inventory_log->associated_object->products->filter(function($product) use ($product_id){
                return $product->product_id === $product_id;
            })->first();
            $new_on_hand = $location_product->quantity_on_hand;
            $previous_on_hand = 0;
        } else if($inventory_log->associated_object_type === Location::class && $inventory_log->associated_object_id != $inventory_log->location_id) {
            // Inventory transfer
            $associated_object_location_product = $inventory_log->associated_object->products->filter(function($product) use ($product_id){
                return $product->product_id === $product_id;
            })->first();
            $new_on_hand = $location_product->quantity_on_hand;
            $previous_on_hand = $location_product->quantity_on_hand - $inventory_log->quantity;
            // Create opposite inventory log when reason is inventory transfer (if dont exists)
            $this->updateOrCreateOppositeInventoryLog($inventory_log, $associated_object_location_product, $product_id);
        } else if($inventory_log->associated_object_type === Shipment::class) {
            // Shipment
            $new_on_hand = $location_product->quantity_on_hand;
            $previous_on_hand = $location_product->quantity_on_hand + $inventory_log->quantity;
        } else if($inventory_log->associated_object_type === PurchaseOrder::class) {
            // Purchase Order
            $new_on_hand = $location_product->quantity_on_hand;
            $previous_on_hand = $location_product->quantity_on_hand - $inventory_log->quantity;
        } else if($inventory_log->associated_object_type === Return_::class) {
            // Return
            $new_on_hand = $location_product->quantity_on_hand;
            $previous_on_hand = $location_product->quantity_on_hand - $inventory_log->quantity;
        }
        // Update new/previous on hand
        $this->updateNewPreviousOnHand($inventory_log, $new_on_hand, $previous_on_hand);
    }

    protected function updateOrCreateOppositeInventoryLog($inventory_log, $associated_object_location_product, $product_id) {
        InventoryLog::updateOrCreate(
            [
                'location_id' => $associated_object_location_product->location_id,
                'associated_object_id' => $inventory_log->location_id,
                'associated_object_type' => Location::class
            ],
            [
                'user_id' => $inventory_log->user_id,
                'location_id' => $associated_object_location_product->location_id,
                'associated_object_id' => $inventory_log->location_id,
                'associated_object_type' => Location::class,
                'product_id' => $product_id,
                'quantity' => $inventory_log->quantity,
                'reason' => $inventory_log->reason,
                'new_on_hand' => $associated_object_location_product->quantity_on_hand,
                'previous_on_hand' => $associated_object_location_product->quantity_on_hand + $inventory_log->quantity
            ]
        );
    }

    protected function updateLocationAssociatedObject($inventory_log, $location_id, $associated_object_id, $associated_object_type)
    {
        $inventory_log->update(
            [
                'location_id' => $location_id,
                'associated_object_id' => $associated_object_id,
                'associated_object_type' => $associated_object_type
            ]
        );
    }

    protected function updateNewPreviousOnHand($inventory_log, $new_on_hand, $previous_on_hand)
    {
        $inventory_log->update(
            [
                'new_on_hand' => $new_on_hand,
                'previous_on_hand' => $previous_on_hand
            ]
        );
    }
}
