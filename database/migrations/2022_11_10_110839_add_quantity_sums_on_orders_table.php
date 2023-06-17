<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddQuantitySumsOnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up(): void
    {
        Schema::table('orders', static function (Blueprint $table) {
            $table->integer('quantity_pending_sum')->default(0);
            $table->integer('quantity_allocated_sum')->default(0);
        });

        Order::chunk(100, static function ($orders) {
            DB::beginTransaction();

            try {
                foreach ($orders as $order) {
                    $quantity_allocated_sum = $order
                        ->orderItems()
                        ->whereDoesntHave('kitOrderItems')
                        ->sum('quantity_allocated');

                    $quantity_pending_sum = $order
                        ->orderItems()
                        ->whereDoesntHave('kitOrderItems')
                        ->sum('quantity_pending');

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update([
                            'quantity_allocated_sum' => $quantity_allocated_sum,
                            'quantity_pending_sum' => $quantity_pending_sum
                        ]);
                }
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
            DB::commit();
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
            $table->dropColumn('quantity_pending_sum', 'quantity_allocated_sum');
        });
    }
}
