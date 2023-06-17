<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetColumnsAsNullableToContactInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_informations', function (Blueprint $table) {
            $table->string('address')->nullable(true)->change();
            $table->string('zip')->nullable(true)->change();
            $table->string('city')->nullable(true)->change();
            $table->string('email')->nullable(true)->change();
            $table->string('phone')->nullable(true)->change();
            $table->string('country_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_informations', function (Blueprint $table) {
            $table->string('address')->nullable(false)->change();
            $table->string('zip')->nullable(false)->change();
            $table->string('city')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('country_id')->nullable(false)->change();
        });
    }
}
