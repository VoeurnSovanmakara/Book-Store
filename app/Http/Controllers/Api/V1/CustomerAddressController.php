<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerAddress\StoreCustomerAddressRequest;
use App\Http\Requests\CustomerAddress\UpdateCustomerAddressRequest;
use App\Http\Resources\CustomerAddressResource;
use App\Models\CustomerAddress;
use App\Services\CustomerAddressService;
use App\Trait\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CustomerAddressService $addressService
    ) {}

    public function myAddresses(Request $request): JsonResponse
    {
        $addresses = $this->addressService->listForCustomer($request->user());

        return $this->success(
            CustomerAddressResource::collection($addresses),
            'Addresses retrieved successfully'
        );
    }

    public function storeMyAddress(StoreCustomerAddressRequest $request): JsonResponse
    {
        $address = $this->addressService->createForCustomer($request->user(), $request->validated());

        return $this->success(
            new CustomerAddressResource($address),
            'Address created successfully',
            201
        );
    }

    public function show(CustomerAddress $address): JsonResponse
    {
        $this->authorize('view', $address);

        return $this->success(new CustomerAddressResource($address), 'Address retrieved successfully');
    }

    public function update(UpdateCustomerAddressRequest $request, CustomerAddress $address): JsonResponse
    {
        $this->authorize('update', $address);

        $address = $this->addressService->update($address, $request->validated());

        return $this->success(new CustomerAddressResource($address), 'Address updated successfully');
    }

    public function destroy(CustomerAddress $address): JsonResponse
    {
        $this->authorize('delete', $address);

        $this->addressService->delete($address);

        return $this->successNoContent('Address deleted successfully');
    }
}
