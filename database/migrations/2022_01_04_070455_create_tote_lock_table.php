<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToteLockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tote_locks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tote_id');
            $table->unsignedInteger('order_id');
            $table->integer('lock_type')->nullable();

            $table->foreign('tote_id')
                ->references('id')
                ->on('totes')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tote_locks');
    }
}
