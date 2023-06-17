<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('contact_information_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('contact_information_id')
                ->references('id')
                ->on('contact_informations')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
