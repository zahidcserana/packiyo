<?php

use App\Models\OrderChannel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_channels', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onUpdate('set null')
                ->onDelete('set null');

            $table->string('name')->default('');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('webhooks', function (Blueprint $table) {
            $table->foreignIdFor(OrderChannel::class)->after('customer_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignIdFor(OrderChannel::class)->after('customer_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['order_channel_id']);
            $table->dropColumn('order_channel_id');
        });

        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropForeign(['order_channel_id']);
            $table->dropColumn('order_channel_id');
        });

        Schema::dropIfExists('order_channels');
    }
}
