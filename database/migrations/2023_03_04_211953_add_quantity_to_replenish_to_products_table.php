<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddQuantityToReplenishToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('quantity_to_replenish')->default(0)->after('quantity_available');
            $table->index('quantity_to_replenish');
        });

        DB::update('UPDATE `products` SET `quantity_to_replenish` = GREATEST(`quantity_allocated` - `quantity_allocated_pickable`, 0)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['quantity_to_replenish']);
            $table->dropColumn('quantity_to_replenish');
        });
    }
}
