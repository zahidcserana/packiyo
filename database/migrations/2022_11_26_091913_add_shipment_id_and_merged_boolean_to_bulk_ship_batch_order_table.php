<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipmentIdAndMergedBooleanToBulkShipBatchOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batch_order', function (Blueprint $table) {
            $table->boolean('labels_merged')->default(false);
            $table->unsignedInteger('shipment_id')->nullable();
            $table->foreign('shipment_id')
                ->references('id')
                ->on('shipments')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_ship_batch_order', function (Blueprint $table) {
            $table->dropForeign(['shipment_id']);
            $table->dropColumn('shipment_id');
            $table->dropColumn('labels_merged');
        });
    }
}
