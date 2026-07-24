<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CustomerService $customerService
    )
    {}

    public function index(): JsonResponse
    {
        $customers = $this->customerService->list();
        return $this->success(
            CustomerResource::collection($customers),
            'Customers retrieved successfully'
        );
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());
        return $this->success(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->success(
            new CustomerResource($customer),
            'Customer retrieved successfully'
        );
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());
        return $this->success(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer);
        return $this->successNoContent('Customer deleted successfully');
    }
}
