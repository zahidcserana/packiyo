<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantitySellAheadToPurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', static function (Blueprint $table) {
            $table->unsignedInteger('quantity_sell_ahead')->default(0)->after('quantity_rejected');
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
            $table->dropColumn('quantity_sell_ahead');
        });
    }
}
