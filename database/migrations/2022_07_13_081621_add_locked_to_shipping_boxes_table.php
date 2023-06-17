<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockedToShippingBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_boxes', function (Blueprint $table) {
            $table->boolean('height_locked')->default(0)->nullable();
            $table->boolean('length_locked')->default(0)->nullable();
            $table->boolean('width_locked')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_boxes', function (Blueprint $table) {
            $table->dropColumn('height_locked');
            $table->dropColumn('length_locked');
            $table->dropColumn('width_locked');
        });
    }
}
