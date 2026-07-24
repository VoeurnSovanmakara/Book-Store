<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'note' => $this->note,
            'type' => $this->type,          // serializes as "HOME" automatically via the enum cast
            'lat' => (float) $this->lat,
            'lng' => (float) $this->lng,
            'detail' => $this->detail,
            'customer_id' => $this->customer_id,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
