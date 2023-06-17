<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('sku');
            $table->index('name');
            $table->index('quantity_on_hand');
            $table->index('quantity_pickable');
            $table->index('quantity_allocated');
            $table->index('quantity_allocated_pickable');
            $table->index('quantity_available');
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['sku']);
            $table->dropIndex(['name']);
            $table->dropIndex(['quantity_on_hand']);
            $table->dropIndex(['quantity_pickable']);
            $table->dropIndex(['quantity_allocated']);
            $table->dropIndex(['quantity_allocated_pickable']);
            $table->dropIndex(['quantity_available']);
            $table->dropIndex(['quantity_backordered']);
        });
    }
}
