<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintedUserAndTimestampToBulkShipBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->unsignedInteger('printed_user_id')->nullable();
            $table->timestamp('printed_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->dropColumn(['printed_user_id', 'printed_timestamp']);
        });
    }
}
