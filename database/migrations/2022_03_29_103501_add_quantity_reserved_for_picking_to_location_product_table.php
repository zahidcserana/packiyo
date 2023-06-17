<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityReservedForPickingToLocationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_product', function (Blueprint $table) {
            $table->integer('quantity_reserved_for_picking')->default(0)->after('quantity_on_hand');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_product', function (Blueprint $table) {
            $table->dropColumn('quantity_reserved_for_picking');
        });
    }
}
