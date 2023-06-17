<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderChannelIdColumnToWebshipperCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webshipper_credentials', function (Blueprint $table) {
            $table->unsignedInteger('order_channel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webshipper_credentials', function (Blueprint $table) {
            $table->dropColumn('order_channel_id');
        });
    }
}
