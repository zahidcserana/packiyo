<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddObjectToContactInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_informations', function (Blueprint $table) {
            $table->string("object_type")->after('id');
            $table->unsignedInteger("object_id")->after('object_type');

            $table->index(["object_type", "object_id"], "object");
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
            $table->dropIndex('object');

            $table->dropColumn('object_id');
            $table->dropColumn('object_type'); 
        });
    }
}
