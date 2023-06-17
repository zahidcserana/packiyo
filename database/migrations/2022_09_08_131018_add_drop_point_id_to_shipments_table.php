<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDropPointIdToShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('shipments', static function (Blueprint $table) {
            $table->unsignedInteger('drop_point_id')->nullable()->after('webshipper_carrier_shipping_method_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('shipments', static function (Blueprint $table) {
            $table->dropColumn('drop_point_id');
        });
    }
}
