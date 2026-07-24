<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthService
{
    public function register(array $data): array
    {
        $customer = Customer::create($data);
        $token = $customer->createToken('customer-api-token')->plainTextToken;

        return [
            'customer' => $customer,
            'token' => $token,
        ];
    }

    public function login(array $credentials): array
    {
        $customer = Customer::where('email', $credentials['email'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $customer->createToken('customer-api-token')->plainTextToken;

        return ['customer' => $customer, 'token' => $token];
    }

    public function logout(Customer $customer): void
    {
        $customer->currentAccessToken()->delete();
    }
}