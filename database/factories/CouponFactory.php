<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('COUPON##??')),
            'amount' => fake()->randomFloat(2, 5, 50),
            'limit_count' => 10,
            'used_count' => 0,
            'effective_date' => now()->subDay(),
            'expired_date' => now()->addMonth(),
        ];
    }
}
