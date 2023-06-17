<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPickableEffectiveColumnToLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('locations', static function (Blueprint $table) {
            $table->boolean('pickable_effective')->after('pickable')->default(0);
        });

        Location::chunk(100, static function ($locations) {
            DB::beginTransaction();

            try {
                foreach ($locations as $location) {
                    $pickableEffective = false;

                    if (is_null($location->locationType)) {
                        $pickableEffective = $location->pickable;
                    } else {
                        $pickableEffective = $location->locationType->pickable ?? $location->pickable;
                    }

                    DB::table('locations')
                        ->where('id', $location->id)
                        ->update([
                            'pickable_effective' => $pickableEffective
                        ]);
                }
            } catch (Exception $e) {
                DB::rollback();
                throw $e;
            }
            DB::commit();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('locations', static function (Blueprint $table) {
            $table->dropColumn('pickable_effective');
        });
    }
}
