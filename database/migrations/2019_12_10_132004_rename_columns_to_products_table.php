<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('on_hand', 'quantity_on_hand');
            $table->renameColumn('allocated', 'quantity_allocated');
            $table->renameColumn('available', 'quantity_available');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('quantity_on_hand', 'on_hand');
            $table->renameColumn('quantity_allocated', 'allocated');
            $table->renameColumn('quantity_available', 'available');
        });
    }
}
