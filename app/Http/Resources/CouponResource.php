<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            'code' => $this->code,
            'amount' => (float) $this->amount,
            'limit_count' => $this->limit_count,
            'used_count' => $this->used_count,
            'remaining' => max($this->limit_count - $this->used_count, 0),
            'effective_date' => $this->effective_date->format('Y-m-d'),
            'expired_date' => $this->expired_date->format('Y-m-d'),
            'is_active' => $this->isValid(),
        ];
    }
}
