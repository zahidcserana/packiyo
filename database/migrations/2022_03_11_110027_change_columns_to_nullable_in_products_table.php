<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsToNullableInProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('weight')->nullable(true)->change();
            $table->float('height')->nullable(true)->change();
            $table->float('length')->nullable(true)->change();
            $table->float('width')->nullable(true)->change();
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
           $table->float('weight')->nullable(false)->change();
           $table->float('height')->nullable(false)->change();
           $table->float('length')->nullable(false)->change();
           $table->float('width')->nullable(false)->change();
        });
    }
}
