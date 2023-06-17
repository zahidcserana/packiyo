<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('shipment_id');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('shipment_id')
                ->references('id')
                ->on('shipments')
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
        Schema::dropIfExists('shipment_trackings');
    }
}
