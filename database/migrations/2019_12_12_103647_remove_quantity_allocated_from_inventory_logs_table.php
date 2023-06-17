<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveQuantityAllocatedFromInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropColumn('quantity_allocated');
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
            $table->float('quantity_allocated')->after('product_id');
        });
    }
}
