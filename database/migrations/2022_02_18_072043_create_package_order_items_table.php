<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedInteger('order_item_id');
            $table->foreign('order_item_id')
                ->references('id')
                ->on('order_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unsignedInteger('package_id');
            $table->foreign('package_id')
                ->references('id')
                ->on('packages')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_order_items');
    }
}
