<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelledColumnToPurchaseOrderStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_statuses', function (Blueprint $table) {
            $table->boolean('cancelled')->default(0)->after('fulfilled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_statuses', function (Blueprint $table) {
            $table->dropColumn('cancelled');
        });
    }
}
