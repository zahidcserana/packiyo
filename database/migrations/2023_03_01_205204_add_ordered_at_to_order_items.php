<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddOrderedAtToOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->timestamp('ordered_at')->nullable();
            $table->index('ordered_at');
        });

        DB::update('UPDATE `order_items` LEFT JOIN `orders` ON `order_items`.`order_id` = `orders`.`id` SET `order_items`.`ordered_at` = `orders`.`ordered_at`');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['ordered_at']);
            $table->dropColumn('ordered_at');
        });
    }
}
