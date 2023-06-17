<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \DB::table('user_roles')->truncate();
        \DB::table('customer_user_roles')->truncate();
        \DB::table('users')->truncate();

        $this->call([
            CountriesSeeder::class,
            UserRolesTableSeeder::class,
            CustomerUserRolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);

        \DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
