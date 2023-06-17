<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippedColumnToBulkShipBatchOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batch_order', function (Blueprint $table) {
            $table->tinyInteger('shipped')->default(0);
            $table->tinyInteger('errors')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_ship_batch_order', function (Blueprint $table) {
            $table->dropColumn('shipped');
            $table->dropColumn('errors');
        });
    }
}
