<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToContactInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact_informations', function (Blueprint $table) {
            $table->index('name');
            $table->index('company_name');
            $table->index('company_number');
            $table->index('address');
            $table->index('address2');
            $table->index('zip');
            $table->index('city');
            $table->index('state');
            $table->index('email');
            $table->index('phone');

            $table->unsignedInteger('country_id')->change();

            DB::update('UPDATE `contact_informations` LEFT JOIN `countries` ON `contact_informations`.`country_id` = `countries`.`id` SET `country_id` = NULL WHERE `countries`.`id` IS NULL');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->cascadeOnDelete()
                ->nullOnDelete();
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
            $table->dropIndex(['name']);
            $table->dropIndex(['company_name']);
            $table->dropIndex(['company_number']);
            $table->dropIndex(['address']);
            $table->dropIndex(['address2']);
            $table->dropIndex(['zip']);
            $table->dropIndex(['city']);
            $table->dropIndex(['state']);
            $table->dropIndex(['email']);
            $table->dropIndex(['phone']);

            $table->dropForeign(['country_id']);
        });

        Schema::table('contact_informations', function (Blueprint $table) {
            $table->string('country_id')->change();

        });
    }
}
