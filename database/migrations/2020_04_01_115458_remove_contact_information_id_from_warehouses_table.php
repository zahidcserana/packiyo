<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveContactInformationIdFromWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign('warehouses_contact_information_id_foreign');

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
        Schema::table('warehouses', function (Blueprint $table) {
            $table->unsignedInteger('contact_information_id')->nullable()->after('id');

            $table->foreign('contact_information_id')
                ->references('id')
                ->on('contact_informations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
