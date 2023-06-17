<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCancelledAndFulfilledFromOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('order_statuses', static function (Blueprint $table) {
            $table->dropColumn(['fulfilled', 'cancelled']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('order_statuses', static function (Blueprint $table) {
            $table->boolean('fulfilled')->default(0);
            $table->boolean('cancelled')->default(0);
        });
    }
}
