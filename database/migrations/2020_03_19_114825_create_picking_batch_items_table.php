<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickingBatchItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picking_batch_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('picking_batch_id');
            $table->unsignedInteger('order_item_id');
            $table->unsignedInteger('location_id');
            $table->float('quantity');
            $table->float('quantity_picked')->default(0);
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('picking_batch_id')
                ->references('id')
                ->on('picking_batches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
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
        Schema::dropIfExists('picking_batch_items');
    }
}
