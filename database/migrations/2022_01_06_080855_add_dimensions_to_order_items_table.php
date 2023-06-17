<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDimensionsToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('sku');
            $table->string('name');
            $table->decimal('price', 12);
            $table->float('weight');
            $table->float('height');
            $table->float('length');
            $table->float('width');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'sku',
                'name',
                'price',
                'weight',
                'height',
                'length',
                'width'
            ]);
        });
    }
}
