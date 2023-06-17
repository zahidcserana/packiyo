<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerUserRole;

class CustomerUserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('customer_user_roles')->insert([
            'id' => CustomerUserRole::ROLE_CUSTOMER_ADMINISTRATOR,
            'name' => 'Customer Administrator',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        \DB::table('customer_user_roles')->insert([
            'id' => CustomerUserRole::ROLE_CUSTOMER_MEMBER,
            'name' => 'Customer Member',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
