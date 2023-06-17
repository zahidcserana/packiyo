<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToToteOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('picking_batch_item_id')
                ->references('id')
                ->on('picking_batch_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
            $table->dropForeign(['location_id']);
            $table->dropForeign(['picking_batch_item_id']);
        });
    }
}
