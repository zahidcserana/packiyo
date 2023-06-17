<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSettingsFieldsOnCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', static function (Blueprint $table) {

            $customers = DB::table('customers')
                ->select(
                    'id',
                    'weight_unit',
                    'dimensions_unit',
                    'packing_slip_textbox',
                    'packing_slip_heading',
                    'label_printer_id',
                    'barcode_printer_id',
                    'packing_slip_printer_id',
                    'auto_print_packing_slip',
                    'locale',
                    'custom_css',
                    'currency'
                )->get();

            foreach( $customers as $customer ){
                \DB::table('customer_settings')->insert([
                    ['customer_id' => $customer->id, 'key' => 'weight_unit', 'value' => $customer->weight_unit],
                    ['customer_id' => $customer->id, 'key' => 'dimensions_unit', 'value' => $customer->dimensions_unit],
                    ['customer_id' => $customer->id, 'key' => 'packing_slip_text', 'value' => $customer->packing_slip_textbox],
                    ['customer_id' => $customer->id, 'key' => 'packing_slip_heading', 'value' => $customer->packing_slip_heading],
                    ['customer_id' => $customer->id, 'key' => 'label_printer_id', 'value' => $customer->label_printer_id],
                    ['customer_id' => $customer->id, 'key' => 'barcode_printer_id', 'value' => $customer->barcode_printer_id],
                    ['customer_id' => $customer->id, 'key' => 'packing_slip_printer_id', 'value' => $customer->packing_slip_printer_id],
                    ['customer_id' => $customer->id, 'key' => 'auto_print_packing_slip', 'value' => $customer->auto_print_packing_slip],
                    ['customer_id' => $customer->id, 'key' => 'locale', 'value' => $customer->locale],
                    ['customer_id' => $customer->id, 'key' => 'customer_css', 'value' => $customer->custom_css],
                    ['customer_id' => $customer->id, 'key' => 'currency', 'value' => $customer->currency],
                ]);
            }

            $table->dropColumn('weight_unit');
            $table->dropColumn('dimensions_unit');
            $table->dropColumn('packing_slip_textbox');
            $table->dropColumn('packing_slip_heading');
            $table->dropForeign('customers_label_printer_id_foreign');
            $table->dropColumn('label_printer_id');

            $table->dropForeign('customers_barcode_printer_id_foreign');
            $table->dropColumn('barcode_printer_id');

            $table->dropForeign('customers_packing_slip_printer_id_foreign');
            $table->dropColumn('packing_slip_printer_id');

            $table->dropColumn('auto_print_packing_slip');

            $table->dropColumn('locale');
            $table->dropColumn('custom_css');
            $table->dropColumn('currency');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', static function (Blueprint $table) {
            $table->string('weight_unit')->default('kg');
            $table->string('dimensions_unit')->default('cm');
            $table->text('packing_slip_heading')->nullable();
            $table->text('packing_slip_textbox')->nullable();
            $table->string('locale')->default('en');
            $table->text('custom_css')->nullable();
            $table->string('currency')->default('USD');

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

            $table->boolean('auto_print_packing_slip')->default(false)->after('packing_slip_printer_id');
        });

        $customerSettings = DB::table('customer_settings')
            ->select(
                'customer_id',
                'key',
                'value'
            )->get();

        foreach ($customerSettings as $customerSetting) {
            if (!$customerSetting->value) {
                continue;
            }

            $key = $customerSetting->key;

            if ($key == 'packing_slip_text') {
                $key = 'packing_slip_textbox';
            } else if ($key == 'customer_css') {
                $key = 'custom_css';
            }

            if (Schema::hasColumns('customers', [$key])) {
                \DB::table('customers')->where('id', $customerSetting->customer_id)->update([$key => $customerSetting->value]);
            }
        }
    }
}
