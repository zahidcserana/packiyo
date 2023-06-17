<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrintJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->softDeletes();

            $table->dateTime('job_start')->nullable();
            $table->dateTime('job_end')->nullable();

            $table->unsignedInteger('printer_id');
            $table->string('job_id_system')->nullable(); // setting this 'string', because not sure if it will be integer in all systems (on mac it is integer)
            $table->string('status')->nullable();
            $table->unsignedInteger('user_id');

            $table->index('job_start');
            $table->index('job_end');
            $table->index('job_id_system');
            $table->index('status');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');

            $table->foreign('printer_id')
                ->references('id')
                ->on('printers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_jobs');
    }
}
