<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webpatser\Countries\Countries;

class ContactInformationFactory extends Factory {

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name,
            'company_name' => $this->faker->company,
            'company_number' => $this->faker->randomNumber(),
            'address' => $this->faker->address,
            'address2' => $this->faker->randomNumber(),
            'zip' => $this->faker->postcode,
            'city' => $this->faker->city,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'country_id' => Countries::inRandomOrder()->first(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
