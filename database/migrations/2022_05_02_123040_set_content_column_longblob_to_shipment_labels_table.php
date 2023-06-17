<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetContentColumnLongblobToShipmentLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::query('ALTER TABLE `shipment_labels` CHANGE `content` `content` LONGBLOB NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::query('ALTER TABLE `shipment_labels` CHANGE `content` `content` BLOB NULL DEFAULT NULL;');
    }
}
