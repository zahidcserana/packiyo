<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('subscription_prices', static function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id');
            $table->string('stripe_product_id');
            $table->unsignedBigInteger('plan_id');

            $table->foreign('plan_id')
                ->references('id')
                ->on('plans')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_prices');
    }
}
