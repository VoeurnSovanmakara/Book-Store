<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerAuthService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CustomerAuthService $customerAuthService
    )
    {}
    
    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->register($request->validated());

        return $this->success([
            'customer' => new CustomerResource($result['customer']),
            'token' => $result['token'],
        ], 'Registered successfully', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->customerAuthService->login($request->validated());

        return $this->success([
            'customer' => new CustomerResource($result['customer']),
            'token' => $result['token'],
        ], 'Logged in successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->customerAuthService->logout($request->user());

        return $this->successNoContent('Logged out successfully');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(new CustomerResource($request->user()), 'Profile retrieved successfully');
    }
}
