<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippedAtColumnToBulkShipBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->timestamp('shipped_at')
                ->after('total_items')
                ->nullable()
                ->default(null);
        });

        DB::statement('UPDATE bulk_ship_batches SET shipped_at = updated_at WHERE shipped = 1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->dropColumn('shipped_at');
        });
    }
}
