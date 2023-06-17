<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebshipperCarrierShippingMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webshipper_carrier_shipping_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('carrier_id');
            $table->unsignedInteger('customer_id');
            $table->string('name');

            $table->timestamps();

            $table->softDeletes();
            $table->integer('webshipper_shipping_rate_id');
            $table->foreign('carrier_id')
                ->references('id')
                ->on('webshipper_carriers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->index('webshipper_shipping_rate_id', 'webshipper_carrier_rate_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webshipper_carrier_shipping_methods');
    }
}
