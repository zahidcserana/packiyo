<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCycleCountBatchItemsTable extends Migration
{
    /**
     * Run the migrations.Migrated:  2022_05_12_080830_create_cycle_count_batch_items_table (77.64ms)

     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle_count_batch_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('cycle_count_batch_id');
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('location_id');
            $table->float('quantity')->nullable();
            $table->float('quantity_confirmed')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('cycle_count_batch_id')
                ->references('id')
                ->on('cycle_count_batches')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
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
        Schema::dropIfExists('cycle_count_batch_items');
    }
}
