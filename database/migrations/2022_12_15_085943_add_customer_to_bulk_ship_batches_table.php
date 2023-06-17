<?php

use App\Models\BulkShipBatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerToBulkShipBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->unsignedInteger('customer_id')->after('id');
        });

        foreach (BulkShipBatch::cursor() as $bulkShipBatch) {
            if ($bulkShipBatch->orders->count() === 0) {
                $bulkShipBatch->delete();
                continue;
            }

            if (! $bulkShipBatch->customer_id) {
                $bulkShipBatch->update([
                    'customer_id' => $bulkShipBatch->orders->first()->customer_id,
                ]);
            }
        }

        Schema::table('bulk_ship_batches', function (Blueprint $table) {
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
    public function down()
    {
        Schema::table('bulk_ship_batches', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
}
