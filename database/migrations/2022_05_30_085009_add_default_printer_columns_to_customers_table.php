<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultPrinterColumnsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('label_printer_id')->nullable()->after('packing_slip_heading');
            $table->foreign('label_printer_id')
                ->references('id')
                ->on('printers')
                ->onUpdate('set null')
                ->onDelete('set null');

            $table->unsignedInteger('barcode_printer_id')->nullable()->after('label_printer_id');
            $table->foreign('barcode_printer_id')
                ->references('id')
                ->on('printers')
                ->onUpdate('set null')
                ->onDelete('set null');

            $table->unsignedInteger('packing_slip_printer_id')->nullable()->after('barcode_printer_id');
            $table->foreign('packing_slip_printer_id')
                ->references('id')
                ->on('printers')
                ->onUpdate('set null')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['label_printer_id']);
            $table->dropColumn('label_printer_id');

            $table->dropForeign(['barcode_printer_id']);
            $table->dropColumn('barcode_printer_id');

            $table->dropForeign(['packing_slip_printer_id']);
            $table->dropColumn('packing_slip_printer_id');
        });
    }
}
