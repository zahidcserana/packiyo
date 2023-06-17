<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssociatedObjectColumnToInventoryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->unsignedInteger('associated_object_id')->nullable()->after('user_id');
            $table->string('associated_object_type')->after('associated_object_id');
            $table->index(['associated_object_id', 'associated_object_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropIndex(['associated_object_id', 'associated_object_type']);
            $table->dropColumn(['associated_object_id', 'associated_object_type']);
        });
    }
}
