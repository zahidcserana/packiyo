<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToteOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tote_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tote_id');
            $table->unsignedInteger('order_item_id');
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('tote_id')
                ->references('id')
                ->on('totes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
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
        Schema::dropIfExists('tote_order_items');
    }
}
