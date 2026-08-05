<?php

use App\Models\Customer;

it('registers a new customer and returns a token', function () {
    $response = $this->postJson('/api/v1/auth/customer/register', [
        'name' => 'Dara',
        'email' => 'dara@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure(['data' => ['customer', 'token']]);

    expect(Customer::where('email', 'dara@example.com')->exists())->toBeTrue();
});

it('rejects registration with duplicate email', function () {
    Customer::factory()->create(['email' => 'taken@example.com']);

    $response = $this->postJson('/api/v1/auth/customer/register', [
        'name' => 'Someone',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});

it('logs in with correct credentials', function () {
    Customer::factory()->create([
        'email' => 'dara@example.com',
        'password' => 'password123',
    ]);

    $response = $this->postJson('/api/v1/auth/customer/login', [
        'email' => 'dara@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)->assertJsonStructure(['data' => ['token']]);
});

it('rejects login with wrong password without revealing which field was wrong', function () {
    Customer::factory()->create(['email' => 'dara@example.com', 'password' => 'password123']);

    $response = $this->postJson('/api/v1/auth/customer/login', [
        'email' => 'dara@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('email');
});

it('blocks access to protected routes without a token', function () {
    $this->getJson('/api/v1/auth/customer/profile')->assertStatus(401);
});

it('logs out and revokes the token', function () {
    $customer = Customer::factory()->create();
    $token = $customer->createToken('test')->plainTextToken;

    expect($customer->tokens()->count())->toBe(1);

    $this->withToken($token)->postJson('/api/v1/auth/customer/logout')->assertStatus(200);

    expect($customer->tokens()->count())->toBe(0);

    // Force Laravel to forget the cached guard/user before making the next request —
    // required in tests because the container persists across simulated requests
    // within a single test method (unlike real HTTP requests, which don't share this state).
    auth()->forgetGuards();

    $this->withToken($token)->getJson('/api/v1/auth/customer/profile')->assertStatus(401);
});