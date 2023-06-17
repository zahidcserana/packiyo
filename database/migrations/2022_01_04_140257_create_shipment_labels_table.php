<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('shipment_id');
            $table->text('size');
            $table->text('url')->nullable();
            $table->binary('content')->nullable();
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
        Schema::dropIfExists('shipment_labels');
    }
}
