<?php

namespace Database\Factories;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => \App\Models\Customer::factory(),
            'customer_address_id' => \App\Models\CustomerAddress::factory(),
            'sub_total_price' => 50.00,
            'discount' => 0,
            'total_payable' => 50.00,
            'status' => 'pending',
        ];
    }
}
