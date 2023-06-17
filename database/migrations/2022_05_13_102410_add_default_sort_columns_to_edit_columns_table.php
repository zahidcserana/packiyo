<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultSortColumnsToEditColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('edit_columns', function (Blueprint $table) {
            $table->integer('order_column')->nullable();
            $table->string('order_direction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edit_columns', function (Blueprint $table) {
            $table->dropColumn(['order_column', 'order_direction']);
        });
    }
}
