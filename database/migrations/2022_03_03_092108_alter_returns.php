<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterReturns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->after('order_id');
            $table->text('reason')->nullable()->change();
            $table->boolean('approved')->default(0)->change();
            $table->text('notes')->nullable()->change();
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('length')->nullable();
            $table->float('width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->text('reason')->change();
            $table->boolean('approved')->change();
            $table->text('notes')->change();
            $table->dropColumn([
                'warehouse_id',
                'weight',
                'height',
                'length',
                'width'
            ]);
        });
    }
}
