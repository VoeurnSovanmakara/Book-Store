<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CustomerAddressController;
use App\Http\Controllers\Api\V1\CustomerAuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    // STAFF AUTH
    Route::post('auth/staff/register', [AuthController::class, 'register']);
    Route::post('auth/staff/login', [AuthController::class, 'login']);

    // CUSTOMER AUTH
    Route::post('auth/customer/register', [CustomerAuthController::class, 'register']);
    Route::post('auth/customer/login', [CustomerAuthController::class, 'login']);

    // STAFF ROUTES
    Route::middleware(['auth:sanctum', 'staff'])->group(function () {
        Route::post('auth/staff/logout', [AuthController::class, 'logout']);
        Route::get('auth/staff/profile', [AuthController::class, 'profile']);

        // AUTHOR
        Route::apiResource('authors', AuthorController::class);
        // BOOK
        Route::apiResource('books', BookController::class);
        // CUSTOMER
        Route::apiResource('customers', CustomerController::class);
    });

    // CUSTOMER ROUTES
    Route::middleware(['auth:sanctum', 'customer'])->group(function () {
        // AUTH
        Route::post('auth/customer/logout', [CustomerAuthController::class, 'logout']);
        Route::get('auth/customer/profile', [CustomerAuthController::class, 'profile']);

        // ADDRESS
        Route::get('my/addresses', [CustomerAddressController::class, 'myAddresses']);
        Route::post('my/addresses', [CustomerAddressController::class, 'storeMyAddress']);
        Route::get('addresses/{address}', [CustomerAddressController::class, 'show']);
        Route::put('addresses/{address}', [CustomerAddressController::class, 'update']);
        Route::delete('addresses/{address}', [CustomerAddressController::class, 'destroy']);
    });
});