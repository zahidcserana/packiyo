<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('number');
            $table->index('fraud_hold');
            $table->index('address_hold');
            $table->index('payment_hold');
            $table->index('operator_hold');
            $table->index('allow_partial');
            $table->index('ready_to_ship');
            $table->index('shipping_method_name');
            $table->index('shipping_method_code');
            $table->index('quantity_pending_sum');
            $table->index('quantity_allocated_sum');
            $table->index('quantity_allocated_pickable_sum');
            $table->index('fulfilled_at');
            $table->index('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['number']);
            $table->dropIndex(['fraud_hold']);
            $table->dropIndex(['address_hold']);
            $table->dropIndex(['payment_hold']);
            $table->dropIndex(['operator_hold']);
            $table->dropIndex(['allow_partial']);
            $table->dropIndex(['ready_to_ship']);
            $table->dropIndex(['shipping_method_name']);
            $table->dropIndex(['shipping_method_code']);
            $table->dropIndex(['quantity_pending_sum']);
            $table->dropIndex(['quantity_allocated_sum']);
            $table->dropIndex(['quantity_allocated_pickable_sum']);
            $table->dropIndex(['fulfilled_at']);
            $table->dropIndex(['cancelled_at']);
        });
    }
}
