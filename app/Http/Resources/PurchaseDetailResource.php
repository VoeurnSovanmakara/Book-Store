<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'book_id' => $this->book_id,
            'book_title' => $this->whenLoaded('book', fn() => $this->book->title),
            'qty' => $this->qty,
            'price' => (float) $this->price, // the snapshot, never live book price
            'line_total' => (float) ($this->price * $this->qty),
        ];
    }
}
