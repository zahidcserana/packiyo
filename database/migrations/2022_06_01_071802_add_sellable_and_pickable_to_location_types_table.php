<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSellableAndPickableToLocationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('location_types', static function (Blueprint $table) {
            $table->boolean('pickable')->nullable();
            $table->boolean('sellable')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('location_types', static function (Blueprint $table) {
            $table->dropColumn(['pickable', 'sellable']);
        });
    }
}
