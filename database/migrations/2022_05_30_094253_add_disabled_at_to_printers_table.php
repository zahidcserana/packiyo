<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisabledAtToPrintersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->timestamp('disabled_at')->nullable(true)->after('customer_id');

            $table->dropColumn('is_default');
            $table->dropColumn('status');
            $table->dropColumn('options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('disabled_at');

            $table->boolean('is_default')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('options')->nullable(true);
        });
    }
}
