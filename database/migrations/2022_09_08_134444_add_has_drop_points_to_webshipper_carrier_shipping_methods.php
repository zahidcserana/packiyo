<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasDropPointsToWebshipperCarrierShippingMethods extends Migration
{
    public function up(): void
    {
        Schema::table('webshipper_carrier_shipping_methods', static function (Blueprint $table) {
            $table->boolean('has_drop_points')->default(false);
        });
    }


    public function down(): void
    {
        Schema::table('webshipper_carrier_shipping_methods', static function (Blueprint $table) {
            $table->dropColumn('has_drop_points');
        });
    }
}
