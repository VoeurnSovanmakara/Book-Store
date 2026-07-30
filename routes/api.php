<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CouponCheckController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\CustomerAddressController;
use App\Http\Controllers\Api\V1\CustomerAuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\PurchaseController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    // ADMIN AUTH
    Route::post('auth/admin/register', [AuthController::class, 'register']);
    Route::post('auth/admin/login', [AuthController::class, 'login']);

    // CUSTOMER AUTH
    Route::post('auth/customer/register', [CustomerAuthController::class, 'register']);
    Route::post('auth/customer/login', [CustomerAuthController::class, 'login']);

    // ADMIN ROUTES
    Route::middleware(['auth:sanctum', 'staff'])->group(function () {
        Route::post('auth/admin/logout', [AuthController::class, 'logout']);
        Route::get('auth/admin/profile', [AuthController::class, 'profile']);

        // AUTHOR
        Route::apiResource('admin/authors', AuthorController::class);
        Route::post('admin/authors/{author}/image', [AuthorController::class, 'uploadImage']);
        Route::delete('admin/authors/{author}/image', [AuthorController::class, 'removeImage']);
        // BOOK
        Route::apiResource('admin/books', BookController::class);
        Route::post('admin/books/{book}/cover', [BookController::class, 'uploadCover']);
        Route::delete('admin/books/{book}/cover', [BookController::class, 'removeCover']);
        // CUSTOMER
        Route::apiResource('admin/customers', CustomerController::class);
        // COUPON
        Route::apiResource('admin/coupons', CouponController::class);
        // PURCHASE
        Route::patch('admin/purchases/{purchase}/status', [PurchaseController::class, 'updateStatus']);
        Route::get('admin/purchases', [PurchaseController::class, 'indexAll']);
    });

    // CUSTOMER ROUTES
    Route::middleware(['auth:sanctum', 'customer'])->group(function () {
        // AUTH
        Route::post('auth/customer/logout', [CustomerAuthController::class, 'logout']);
        Route::get('auth/customer/profile', [CustomerAuthController::class, 'profile']);

        // BOOK
        Route::get('customer/books', [BookController::class, 'index']);

        // PURCHASE
        Route::get('customer/purchases', [PurchaseController::class, 'index']);
        Route::post('customer/purchases', [PurchaseController::class, 'store']);
        Route::get('customer/purchases/{purchase}', [PurchaseController::class, 'show']);

        // ADDRESS
        Route::get('customer/addresses', [CustomerAddressController::class, 'myAddresses']);
        Route::post('customer/addresses', [CustomerAddressController::class, 'storeMyAddress']);
        Route::get('customer/addresses/{address}', [CustomerAddressController::class, 'show']);
        Route::put('customer/addresses/{address}', [CustomerAddressController::class, 'update']);
        Route::delete('customer/addresses/{address}', [CustomerAddressController::class, 'destroy']);

        // COUPON
        Route::post('customer/coupons/check', [CouponCheckController::class, 'check']);
    });
});