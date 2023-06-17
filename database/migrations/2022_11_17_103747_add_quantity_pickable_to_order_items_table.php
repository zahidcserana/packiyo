<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityPickableToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_items', static function (Blueprint $table) {
            $table->integer('quantity_allocated_pickable')->after('quantity_allocated')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_items', static function (Blueprint $table) {
            $table->dropColumn('quantity_allocated_pickable');
        });
    }
}
