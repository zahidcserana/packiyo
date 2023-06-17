<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomsInformationToEasypostCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('easypost_credentials', function (Blueprint $table) {
            $table->string('customs_signer')->default('');
            $table->string('contents_type')->default('');
            $table->string('eel_pfc')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('easypost_credentials', function (Blueprint $table) {
            $table->dropColumn('eel_pfc');
            $table->dropColumn('contents_type');
            $table->dropColumn('customs_signer');
        });
    }
}
