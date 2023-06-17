<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShipmentLabelTypeColumnToShipmentLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('shipment_labels', static function (Blueprint $table) {
            $table->renameColumn('type', 'document_type');
        });

        Schema::table('shipment_labels', static function (Blueprint $table) {
            $table->string('type')->after('document_type')->default('shipping');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('shipment_labels', static function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('document_type', 'type');
        });
    }
}
