<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('order_id');
            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('shipping_box_id');
            $table->foreign('shipping_box_id')
                ->references('id')
                ->on('shipping_boxes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('webshipper_carrier_shipping_methods_id');
            $table->foreign('webshipper_carrier_shipping_methods_id')
                ->references('id')
                ->on('webshipper_carrier_shipping_methods')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->float('weight');
            $table->float('length');
            $table->float('width');
            $table->float('height');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
