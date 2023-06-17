<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivedAtColumnToPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('purchase_orders', static function (Blueprint $table) {
            if (!Schema::hasColumn($table->getTable(), 'received_at')) {
                $table->timestamp('received_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('purchase_orders', static function (Blueprint $table) {
            $table->dropColumn('received_at');
        });
    }
}
