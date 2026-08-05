<?php

namespace Database\Factories;

use App\Enums\AddressType;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerAddress>
 */
class CustomerAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'type' => fake()->randomElement(AddressType::cases())->value,
            'lat' => fake()->latitude(),
            'lng' => fake()->longitude(),
            'detail' => fake()->address(),
        ];
    }
}
