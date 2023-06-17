<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrintersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->softDeletes();

            $table->string('name');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('tmp_user_id');

            $table->boolean('is_default');
            $table->string('status');
            $table->text('options');

            $table->index('is_default');
            $table->index('status');


            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('printers');
    }
}
