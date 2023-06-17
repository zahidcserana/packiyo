<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameQuantityAvailableInInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->renameColumn('quantity_available', 'quantity');
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
            $table->renameColumn('quantity', 'quantity_available');
        });
    }
}
