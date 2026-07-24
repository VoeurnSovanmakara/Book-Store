<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Collection;

class CustomerAddressService
{
    public function listForCustomer(Customer $customer): Collection
    {
        return $customer->addresses()->latest()->get();
    }

    public function createForCustomer(Customer $customer, array $data): CustomerAddress
    {
        return $customer->addresses()->create($data);
    }

    public function update(CustomerAddress $address, array $data): CustomerAddress
    {
        $address->update($data);
        return $address;
    }

    public function delete(CustomerAddress $address): void
    {
        $address->delete();
    }
}