<?php

use App\Models\Tote;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationIdAndToteIdColumnToPackageOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_order_items', function (Blueprint $table) {
            $table->unsignedInteger('location_id')
                ->nullable()
                ->after('package_id');
            $table->foreign('location_id')
                ->references('id')
                ->on('locations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignIdFor(Tote::class)
                ->nullable()
                ->after('location_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('location_id');
            $table->dropConstrainedForeignId('tote_id');
        });
    }
}
