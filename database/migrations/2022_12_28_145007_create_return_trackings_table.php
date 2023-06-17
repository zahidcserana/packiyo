<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('return_id');
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('return_id')
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
        Schema::dropIfExists('return_trackings');
    }
}
