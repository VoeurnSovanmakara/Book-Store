<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'status' => $this->status,
            'sub_total_price' => (float) $this->sub_total_price,
            'discount' => (float) $this->discount,
            'total_payable' => (float) $this->total_payable,
            'address' => new CustomerAddressResource($this->whenLoaded('address')),
            'items' => PurchaseDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
