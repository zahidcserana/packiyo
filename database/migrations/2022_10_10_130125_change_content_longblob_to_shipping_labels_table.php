<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeContentLongblobToShippingLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE `shipment_labels` CHANGE `content` `content` LONGBLOB NULL DEFAULT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_labels', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `shipment_labels` CHANGE `content` `content` BLOB NULL DEFAULT NULL');
        });
    }
}
