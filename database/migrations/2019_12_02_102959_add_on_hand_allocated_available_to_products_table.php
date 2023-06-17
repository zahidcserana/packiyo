<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnHandAllocatedAvailableToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('on_hand')->default(0)->after('notes');
            $table->integer('allocated')->default(0)->after('on_hand');
            $table->integer('available')->default(0)->after('allocated');
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
            $table->dropColumn('on_hand');
            $table->dropColumn('allocated');
            $table->dropColumn('available');
        });
    }
}
