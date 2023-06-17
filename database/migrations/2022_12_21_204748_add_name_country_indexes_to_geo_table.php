<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameCountryIndexesToGeoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geo', function (Blueprint $table) {
            $table->index('name');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geo', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['country']);
        });
    }
}
