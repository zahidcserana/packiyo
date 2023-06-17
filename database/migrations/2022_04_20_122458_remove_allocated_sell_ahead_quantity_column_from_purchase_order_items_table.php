<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAllocatedSellAheadQuantityColumnFromPurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', static function (Blueprint $table) {
            $table->dropColumn('quantity_allocated_sell_ahead');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', static function (Blueprint $table) {
            $table->unsignedInteger('quantity_allocated_sell_ahead')->default(0)->after('quantity_sell_ahead');
        });
    }
}
