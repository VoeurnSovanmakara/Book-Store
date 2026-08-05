<?php

use App\Jobs\SendPurchasePaidEmail;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('allows staff to transition pending to paid and dispatches the event job', function () {
    Queue::fake();

    $staff = User::factory()->create();
    $purchase = Purchase::factory()->create(['status' => 'pending']);
    $token = $staff->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/admin/purchases/{$purchase->id}/status", ['status' => 'paid'])
        ->assertStatus(200)
        ->assertJsonPath('data.status', 'paid');

    Queue::assertPushed(SendPurchasePaidEmail::class);
});

it('rejects an invalid status transition', function () {
    $staff = User::factory()->create();
    $purchase = Purchase::factory()->create(['status' => 'cancelled']); // terminal state
    $token = $staff->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/admin/purchases/{$purchase->id}/status", ['status' => 'paid'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('status');
});

it('blocks customers from updating purchase status', function () {
    $customer = \App\Models\Customer::factory()->create();
    $purchase = Purchase::factory()->create(['status' => 'pending']);
    $token = $customer->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->patchJson("/api/v1/admin/purchases/{$purchase->id}/status", ['status' => 'paid'])
        ->assertStatus(403);
});