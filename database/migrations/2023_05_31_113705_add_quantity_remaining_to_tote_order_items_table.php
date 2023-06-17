<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityRemainingToToteOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->unsignedInteger('quantity_remaining')
                ->after('quantity_removed')
                ->default(0)
                ->index();
        });

        DB::update('UPDATE `tote_order_items` SET `quantity_removed` = `quantity` WHERE `quantity_removed` > `quantity`');
        DB::update('UPDATE `tote_order_items` SET `quantity_remaining` = `quantity` - `quantity_removed`');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tote_order_items', function (Blueprint $table) {
            $table->dropIndex(['quantity_remaining']);
            $table->dropColumn('quantity_remaining');
        });
    }
}
