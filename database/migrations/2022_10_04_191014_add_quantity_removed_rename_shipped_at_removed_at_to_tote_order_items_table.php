<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityRemovedRenameShippedAtRemovedAtToToteOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->integer('quantity_removed')->default(0)->after('quantity');
            $table->renameColumn('shipped_at', 'removed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->renameColumn('removed_at', 'shipped_at');
            $table->dropColumn('quantity_removed');
        });
    }
}
