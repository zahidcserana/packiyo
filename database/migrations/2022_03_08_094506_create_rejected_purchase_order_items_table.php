<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectedPurchaseOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('rejected_purchase_order_items', static function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_order_item_id');
            $table->integer('quantity');
            $table->string('reason');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('purchase_order_item_id')
                ->references('id')
                ->on('purchase_order_items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('rejected_purchase_order_items');
    }
}
