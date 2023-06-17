<?php

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShipmentItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderItemIdToShipmentItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->unsignedInteger('order_item_id')
                ->nullable(true)
                ->after('shipment_id');

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        $shipmentItems = ShipmentItem::all();

        foreach ($shipmentItems as $shipmentItem) {
            $orderItem = OrderItem::where('order_id', $shipmentItem->shipment->order_id)
                ->where('product_id', $shipmentItem->product_id)
                ->first();

            if ($orderItem) {
                $shipmentItem->order_item_id = $orderItem->id;
                $shipmentItem->save();
            }
        }

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipment_items', function (Blueprint $table) {
            $table->unsignedInteger('product_id')
                ->nullable(true)
                ->after('shipment_id');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });

        $shipmentItems = ShipmentItem::all();

        foreach ($shipmentItems as $shipmentItem) {
            $orderItem = OrderItem::find($shipmentItem->order_item_id);

            if ($orderItem) {
                $shipmentItem->product_id = $orderItem->product_id;
                $shipmentItem->save();
            }
        }

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['order_item_id']);
            $table->dropColumn('order_item_id');
        });
    }
}
