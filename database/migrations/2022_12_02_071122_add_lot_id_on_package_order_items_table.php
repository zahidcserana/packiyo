<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotIdOnPackageOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_order_items', static function (Blueprint $table) {

            $table->unsignedBigInteger('lot_id')->nullable();
            $table->foreign('lot_id')
                ->references('id')
                ->on('lots')
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
        Schema::table('package_order_items', static function (Blueprint $table) {
            $table->dropForeign('package_order_items_lot_id_foreign');
            $table->dropColumn('lot_id');
        });
    }
}
