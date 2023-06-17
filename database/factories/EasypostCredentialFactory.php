<?php

namespace Database\Factories;

use App\Models\EasypostCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

class EasypostCredentialFactory extends Factory {

    public function definition()
    {
        return [
            'api_base_url' => 'https://api.easypost.com/v2/',
            'api_key' => 'EZAKe12b9fb924364df08198ec6ba637dd6eDPn5vT9xEeM0KtHW1xYl2w',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
