<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPickingCartIdToTotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('totes', function (Blueprint $table) {
            $table->unsignedBigInteger('picking_cart_id')->nullable();

            $table->foreign('picking_cart_id')
                ->references('id')
                ->on('picking_carts')
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
        Schema::table('totes', function (Blueprint $table) {
            $table->dropForeign('totes_picking_cart_id_foreign');
            $table->dropColumn('picking_cart_id');
        });
    }
}
