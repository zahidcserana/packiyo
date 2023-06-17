<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnHandColumnsToInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->integer('previous_on_hand')->default(0)->after('product_id');
            $table->integer('new_on_hand')->default(0)->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropColumn('previous_on_hand');
            $table->dropColumn('new_on_hand');
        });
    }
}
