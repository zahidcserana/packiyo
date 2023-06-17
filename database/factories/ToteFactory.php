<?php

/** @var Factory $factory */

use App\Models\Warehouse;
use App\Tote;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Tote::class, function (Faker $faker) {
    $warehouse = Warehouse::all()->random()->id;

    return [
        'name' => $faker->unique()->name,
        'barcode' => 'TOTE' . $faker->unique()->name,
        'created_at' => now(),
        'updated_at' => now(),
        'warehouse' => $warehouse
    ];
});
