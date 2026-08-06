<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::create([
            'code' => 'WELCOME10',
            'amount' => 10.00,
            'limit_count' => 100,
            'used_count' => 0,
            'effective_date' => now()->subDay(),
            'expired_date' => now()->addMonths(3),
        ]);

        Coupon::create([
            'code' => 'EXPIRED5',
            'amount' => 5.00,
            'limit_count' => 50,
            'used_count' => 0,
            'effective_date' => now()->subMonths(2),
            'expired_date' => now()->subDay(), // already expired — deliberately, for testing rejection
        ]);

        Coupon::create([
            'code' => 'LIMITREACHED',
            'amount' => 15.00,
            'limit_count' => 1,
            'used_count' => 1, // already at its limit — deliberately, for testing rejection
            'effective_date' => now()->subDay(),
            'expired_date' => now()->addMonth(),
        ]);
    }
}
