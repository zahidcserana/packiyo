<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameHandOnToLocationProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_product', function (Blueprint $table) {
            $table->renameColumn('on_hand', 'quantity_on_hand');
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
            $table->renameColumn('quantity_on_hand', 'on_hand');
        });
    }
}
