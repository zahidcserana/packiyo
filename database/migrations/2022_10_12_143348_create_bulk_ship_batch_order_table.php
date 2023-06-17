<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkShipBatchOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_ship_batch_order', function (Blueprint $table) {
            $table->string('bulk_ship_batch_batch_key');
            $table->foreign('bulk_ship_batch_batch_key')
                ->references('batch_key')
                ->on('bulk_ship_batches')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
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
        Schema::dropIfExists('bulk_ship_batch_order');
    }
}
