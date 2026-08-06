<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@bookstore.test',
            'password' => Hash::make('password'),
            'role' => StaffRole::ADMIN,
        ]);

        User::create([
            'name' => 'Store Manager',
            'email' => 'manager@bookstore.test',
            'password' => Hash::make('password'),
            'role' => StaffRole::MANAGER,
        ]);

        User::create([
            'name' => 'Staff Member',
            'email' => 'member@bookstore.test',
            'password' => Hash::make('password'),
            'role' => StaffRole::MEMBER,
        ]);
    }
}
