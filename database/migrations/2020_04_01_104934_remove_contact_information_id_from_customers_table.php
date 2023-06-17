<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveContactInformationIdFromCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_contact_information_id_foreign');

            $table->dropColumn('contact_information_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('contact_information_id')->nullable()->after('id');

            $table->foreign('contact_information_id')
                ->references('id')
                ->on('contact_informations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
