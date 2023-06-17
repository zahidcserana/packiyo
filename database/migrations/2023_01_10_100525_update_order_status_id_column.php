<?php

use App\Models\OrderStatus;
use App\Models\PurchaseOrderStatus;
use App\Models\ReturnStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderStatusIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->unsignedInteger('order_status_id')->nullable()->change();

            $table->dropForeign('orders_order_status_id_foreign');

            $table->foreign('order_status_id')
                ->references('id')
                ->on('order_statuses')
                ->onDelete('set null');
        });

        Schema::table('purchase_orders', static function (Blueprint $table) {
            $table->unsignedInteger('purchase_order_status_id')->nullable()->change();

            $table->dropForeign('purchase_orders_purchase_order_status_id_foreign');

            $table->foreign('purchase_order_status_id')
                ->references('id')
                ->on('purchase_order_statuses')
                ->onDelete('set null');
        });

        Schema::table('returns', static function (Blueprint $table) {
            $table->dropForeign('returns_return_status_id_foreign');

            $table->foreign('return_status_id')
                ->references('id')
                ->on('return_statuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->unsignedInteger('order_status_id')->nullable(false)->change();

            $table->dropForeign('orders_order_status_id_foreign');

            $table->foreign('order_status_id')
                ->references('id')
                ->on('order_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('purchase_orders', static function (Blueprint $table) {
            $table->unsignedInteger('purchase_order_status_id')->nullable(false)->change();

            $table->dropForeign('purchase_orders_purchase_order_status_id_foreign');

            $table->foreign('purchase_order_status_id')
                ->references('id')
                ->on('purchase_order_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('returns', static function (Blueprint $table) {
            $table->dropForeign('returns_return_status_id_foreign');

            $table->foreign('return_status_id')
                ->references('id')
                ->on('return_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
