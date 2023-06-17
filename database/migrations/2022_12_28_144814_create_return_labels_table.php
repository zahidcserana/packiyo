<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('return_id');
            $table->text('size');
            $table->text('url')->nullable();
            $table->binary('content')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('return_id')
                ->references('id')
                ->on('shipments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `return_labels` CHANGE `content` `content` LONGBLOB NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_labels');
    }
}
