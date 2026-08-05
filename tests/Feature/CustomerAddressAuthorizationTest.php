<?php

use App\Models\Customer;
use App\Models\CustomerAddress;

it('prevents a customer from updating another customer\'s address', function () {
    $customerA = Customer::factory()->create();
    $customerB = Customer::factory()->create();
    $addressA = CustomerAddress::factory()->for($customerA)->create();

    $tokenB = $customerB->createToken('test')->plainTextToken;

    $this->withToken($tokenB)
        ->putJson("/api/v1/customer/addresses/{$addressA->id}", ['name' => 'Hacked'])
        ->assertStatus(403);

    expect($addressA->fresh()->name)->not->toBe('Hacked');
});

it('prevents a customer from deleting another customer\'s address', function () {
    $customerA = Customer::factory()->create();
    $customerB = Customer::factory()->create();
    $addressA = CustomerAddress::factory()->for($customerA)->create();

    $tokenB = $customerB->createToken('test')->plainTextToken;

    $this->withToken($tokenB)
        ->deleteJson("/api/v1/customer/addresses/{$addressA->id}")
        ->assertStatus(403);

    expect(CustomerAddress::find($addressA->id))->not->toBeNull();
});

it('allows a customer to update their own address', function () {
    $customer = Customer::factory()->create();
    $address = CustomerAddress::factory()->for($customer)->create();
    $token = $customer->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->putJson("/api/v1/customer/addresses/{$address->id}", ['name' => 'Updated Name'])
        ->assertStatus(200);

    expect($address->fresh()->name)->toBe('Updated Name');
});