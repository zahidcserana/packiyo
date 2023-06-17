<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('shipping_contact_information_id');
            $table->unsignedInteger('billing_contact_information_id');
            $table->unsignedInteger('order_status_id');
            $table->string('number');
            $table->timestamp('ordered_at')->nullable();
            $table->timestamp('required_shipping_date_at')->nullable();
            $table->timestamp('shipping_date_before_at')->nullable();
            $table->text('notes')->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->softDeletes();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('shipping_contact_information_id')
                ->references('id')
                ->on('contact_informations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('billing_contact_information_id')
                ->references('id')
                ->on('contact_informations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('order_status_id')
                ->references('id')
                ->on('order_statuses')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
