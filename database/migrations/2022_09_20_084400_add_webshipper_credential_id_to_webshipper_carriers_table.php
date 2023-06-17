<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebshipperCredentialIdToWebshipperCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webshipper_carriers', function (Blueprint $table) {
            $table->unsignedInteger('webshipper_credential_id')->nullable();
            $table->foreign('webshipper_credential_id')
                ->references('id')
                ->on('webshipper_credentials')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webshipper_carriers', function (Blueprint $table) {
            $table->dropForeign(['webshipper_credential_id']);
            $table->dropColumn('webshipper_credential_id');
        });
    }
}
