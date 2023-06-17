<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CustomerBillingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('customer_billing_details', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_holder_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address');
            $table->string('address2')->nullable();
            $table->string('postal_code');
            $table->string('city')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->string('state')->nullable();
            $table->unsignedInteger('customer_id');
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
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
        Schema::dropIfExists('customer_billing_details');
    }
}
