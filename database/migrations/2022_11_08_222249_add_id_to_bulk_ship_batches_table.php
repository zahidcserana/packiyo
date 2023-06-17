<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdToBulkShipBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batch_order', function(Blueprint $table) {
            $table->dropForeign(['bulk_ship_batch_batch_key']);
        });

        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->dropPrimary();
        });

        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->id()->first();
            $table->string('label');
        });

        Schema::table('bulk_ship_batch_order', function(Blueprint $table) {
            $table->foreignId('bulk_ship_batch_id');
            $table->dropColumn('bulk_ship_batch_batch_key');
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
            $table->integer('id')->change();
            $table->dropColumn('label');
            $table->dropPrimary();
        });

        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->primary('batch_key');
        });

        Schema::table('bulk_ship_batch_order', function(Blueprint $table) {
            $table->dropColumn('bulk_ship_batch_id');
            $table->string('bulk_ship_batch_batch_key');
        });
    }
}
