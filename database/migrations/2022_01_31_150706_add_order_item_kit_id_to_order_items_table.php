<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderItemKitIdToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('order_item_kit_id')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_item_kit_id')
                ->references('id')
                ->on('order_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_item_kit_id']);
            $table->dropColumn('order_item_kit_id');
        });
    }
}
