<?php

namespace Database\Seeders;

use App\Models\ContactInformation;
use App\Models\Customer;
use App\Models\CustomerUserRole;
use App\Models\TaskType;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::factory()->create([
            'email' => 'admin@packiyo.com',
            'password' => \Hash::make('admin@ninjabring.com'),
            'user_role_id' => UserRole::ROLE_ADMINISTRATOR
        ]);

        ContactInformation::factory()->create([
            'name' => 'Admin',
            'company_name' => 'Packiyo',
            'company_number' => '',
            'address' => '',
            'address2' => '',
            'zip' => '',
            'city' => '',
            'email' => $admin->email,
            'phone' => '',
            'object_type' => get_class($admin),
            'object_id' => $admin->id
        ]);
    }
}
