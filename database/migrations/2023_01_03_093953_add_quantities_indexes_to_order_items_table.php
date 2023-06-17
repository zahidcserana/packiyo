<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantitiesIndexesToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('quantity');
            $table->index('quantity_shipped');
            $table->index('quantity_returned');
            $table->index('quantity_pending');
            $table->index('quantity_allocated');
            $table->index('quantity_allocated_pickable');
            $table->index('quantity_backordered');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['quantity_backordered']);
            $table->dropIndex(['quantity_allocated_pickable']);
            $table->dropIndex(['quantity_allocated']);
            $table->dropIndex(['quantity_pending']);
            $table->dropIndex(['quantity_returned']);
            $table->dropIndex(['quantity_shipped']);
            $table->dropIndex(['quantity']);
        });
    }
}
