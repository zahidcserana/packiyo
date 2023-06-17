<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_informations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('company_number')->nullable();
            $table->string('address');
            $table->string('address2')->nullable();
            $table->string('zip');
            $table->string('city');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_informations');
    }
}
