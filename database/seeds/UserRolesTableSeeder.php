<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserRole;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('user_roles')->insert([
            'id' => UserRole::ROLE_ADMINISTRATOR,
            'name' => 'Administrator',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        \DB::table('user_roles')->insert([
            'id' => UserRole::ROLE_DEFAULT,
            'name' => 'Member',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
