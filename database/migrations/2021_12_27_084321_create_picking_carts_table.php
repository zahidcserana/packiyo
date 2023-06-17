<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePickingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picking_carts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('warehouse_id');
            $table->string('name');
            $table->string('barcode');
            $table->unsignedInteger('number_of_totes');
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
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
        Schema::dropIfExists('picking_carts');
    }
}
