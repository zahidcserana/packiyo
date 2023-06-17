<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackedUserAndTimestampToBulkShipBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->unsignedInteger('packed_user_id')->nullable();
            $table->timestamp('packed_at');
            $table->renameColumn('printed_timestamp', 'printed_at');
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
            $table->dropColumn(['packed_user_id', 'packed_at']);
            $table->renameColumn('printed_at', 'printed_timestamp');
        });
    }
}
