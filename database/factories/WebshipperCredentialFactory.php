<?php

namespace Database\Factories;

use App\Models\WebshipperCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebshipperCredentialFactory extends Factory {

    public function definition()
    {
        return [
            'api_base_url' => 'https://fena.api.webshipper.io/v2/',
            'api_key' => '3ac57f576765b486945db7a7752a6dfa4e4db8f8432958079d7c9c769aa9176c',
            'created_at' => now(),
            'updated_at' => now(),
            'order_channel_id' => 1
        ];
    }
}
