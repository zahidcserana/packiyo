<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnShippingMethodIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->unsignedBigInteger('return_shipping_method_id')->nullable()->after('shipping_method_id');

            $table->foreign(['return_shipping_method_id'])
                ->references('id')
                ->on('shipping_methods')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->dropForeign(['return_shipping_method_id']);
            $table->dropColumn('return_shipping_method_id');
        });
    }
}
