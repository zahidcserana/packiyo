<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskableToTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string("taskable_type")->nullable()->after('id');
            $table->unsignedInteger("taskable_id")->nullable()->after('taskable_type');

            $table->index(["taskable_type", "taskable_id"], "taskable");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('taskable');

            $table->dropColumn('taskable_id');
            $table->dropColumn('taskable_type');            
        });
    }
}
