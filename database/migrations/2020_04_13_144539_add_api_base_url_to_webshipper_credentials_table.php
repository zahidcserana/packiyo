<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiBaseUrlToWebshipperCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webshipper_credentials', function (Blueprint $table) {
            $table->string('api_base_url')->after('customer_id');
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
            $table->dropColumn('api_base_url');
        });
    }
}
