<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotesTagsAndHoldToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('gift_notes')->nullable();
            $table->text('tags')->nullable();
            $table->boolean('fraud_hold')->default(false);
            $table->boolean('address_hold')->default(false);
            $table->boolean('payment_hold')->default(false);
            $table->boolean('operator_hold')->default(false);
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
            $table->dropColumn('gift_notes');
            $table->dropColumn('tags');
            $table->dropColumn('fraud_hold');
            $table->dropColumn('address_hold');
            $table->dropColumn('payment_hold');
            $table->dropColumn('operator_hold');
        });
    }
}
