<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameRoleIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_role_id_foreign');

            $table->renameColumn('role_id', 'user_role_id');

            $table->foreign('user_role_id')
                ->references('id')
                ->on('user_roles')
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_user_role_id_foreign');

            $table->renameColumn('user_role_id', 'role_id');

            $table->foreign('role_id')
                ->references('id')
                ->on('user_roles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
