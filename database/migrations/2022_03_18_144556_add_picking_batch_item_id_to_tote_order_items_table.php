<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickingBatchItemIdToToteOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->unsignedInteger('picking_batch_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->dropColumn('picking_batch_item_id');
        });
    }
}
