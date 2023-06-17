<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebshipperShippingCarrierIdToWebshipperCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webshipper_carriers', function (Blueprint $table) {
            $table->integer('webshipper_shipping_carrier_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webshipper_carriers', function (Blueprint $table) {
            $table->dropColumn('webshipper_shipping_carrier_id');
        });
    }
}
