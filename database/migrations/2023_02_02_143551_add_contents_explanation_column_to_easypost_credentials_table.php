<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContentsExplanationColumnToEasypostCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('easypost_credentials', function (Blueprint $table) {
            $table->string('contents_explanation')->nullable();
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
            $table->dropColumn('contents_explanation');
        });
    }
}
