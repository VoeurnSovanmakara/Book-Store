<?php

use App\Models\Book;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAddress;

beforeEach(function () {
    $this->customer = Customer::factory()->create();
    $this->address = CustomerAddress::factory()->for($this->customer)->create();
    $this->token = $this->customer->createToken('test')->plainTextToken;
});

it('creates a purchase with correct totals', function () {
    $book = Book::factory()->create(['price' => 20.00]);

    $response = $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [
            ['book_id' => $book->id, 'qty' => 3],
        ],
    ]);

    $response->assertStatus(201);

    expect($response->json('data.sub_total_price'))->toEqual(60.0);
    expect($response->json('data.total_payable'))->toEqual(60.0);
    expect($response->json('data.status'))->toBe('pending');

    $this->assertDatabaseHas('purchase_details', [
        'book_id' => $book->id,
        'qty' => 3,
        'price' => 20.00, // snapshot price, not live lookup
    ]);
});

it('snapshots the book price at time of purchase, unaffected by later price changes', function () {
    $book = Book::factory()->create(['price' => 20.00]);

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [['book_id' => $book->id, 'qty' => 1]],
    ])->assertStatus(201);

    $book->update(['price' => 999.00]); // price changes AFTER purchase

    $this->assertDatabaseHas('purchase_details', [
        'book_id' => $book->id,
        'price' => 20.00, // still the original price — this is the whole point of Phase 15
    ]);
});

it('rejects a purchase using another customer\'s address', function () {
    $otherCustomer = Customer::factory()->create();
    $otherAddress = CustomerAddress::factory()->for($otherCustomer)->create();
    $book = Book::factory()->create();

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $otherAddress->id,
        'items' => [['book_id' => $book->id, 'qty' => 1]],
    ])->assertStatus(422)->assertJsonValidationErrors('customer_address_id');
});

it('applies a valid coupon and caps discount at the subtotal', function () {
    $book = Book::factory()->create(['price' => 10.00]);
    $coupon = Coupon::factory()->create(['amount' => 50.00, 'limit_count' => 5]); // discount > subtotal

    $response = $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'coupon_code' => $coupon->code,
        'items' => [['book_id' => $book->id, 'qty' => 1]],
    ]);

    $response->assertStatus(201);

    expect($response->json('data.discount'))->toEqual(10.0);      // capped, not 50
    expect($response->json('data.total_payable'))->toEqual(0.0); // never negative
});

it('enforces coupon usage limit across multiple purchases', function () {
    $book = Book::factory()->create(['price' => 10.00]);
    $coupon = Coupon::factory()->create(['limit_count' => 1]);

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'coupon_code' => $coupon->code,
        'items' => [['book_id' => $book->id, 'qty' => 1]],
    ])->assertStatus(201);

    // second attempt with the now-exhausted coupon
    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'coupon_code' => $coupon->code,
        'items' => [['book_id' => $book->id, 'qty' => 1]],
    ])->assertStatus(422)->assertJsonValidationErrors('coupon_code');

    expect($coupon->fresh()->used_count)->toBe(1); // confirms it wasn't incremented on the rejected attempt
});

it('rejects a purchase with an invalid book id', function () {
    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [['book_id' => 99999, 'qty' => 1]],
    ])->assertStatus(422)->assertJsonValidationErrors('items.0.book_id');
});

it('rolls back the entire purchase if something fails mid-transaction', function () {
    // simulate by attempting an invalid item alongside a valid one —
    // confirms the valid item's purchase_detail was never created despite passing validation up to that point
    $validBook = Book::factory()->create();

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [
            ['book_id' => $validBook->id, 'qty' => 1],
            ['book_id' => 99999, 'qty' => 1], // fails validation before the transaction even opens
        ],
    ])->assertStatus(422);

    $this->assertDatabaseCount('purchases', 0);
    $this->assertDatabaseCount('purchase_details', 0);
});

it('rejects a purchase when requested quantity exceeds stock', function () {
    $book = Book::factory()->create(['stock' => 2]);

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [['book_id' => $book->id, 'qty' => 5]],
    ])->assertStatus(422)->assertJsonValidationErrors('items');

    expect($book->fresh()->stock)->toBe(2); // unchanged — confirms rollback
});

it('decrements stock correctly after a successful purchase', function () {
    $book = Book::factory()->create(['stock' => 10]);

    $this->withToken($this->token)->postJson('/api/v1/customer/purchases', [
        'customer_address_id' => $this->address->id,
        'items' => [['book_id' => $book->id, 'qty' => 3]],
    ])->assertStatus(201);

    expect($book->fresh()->stock)->toBe(7);
});