<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebshipperCarrierShippingMethodIdToShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipments', function (Blueprint $table) {

            $table->unsignedInteger('webshipper_carrier_shipping_method_id')->nullable()->after('webshipper_shipment_id');

            $table->foreign('webshipper_carrier_shipping_method_id')
                ->references('id')
                ->on('shipments')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropForeign(['webshipper_carrier_shipping_method_id']);
            $table->dropColumn('webshipper_carrier_shipping_method_id');
        });
    }
}
