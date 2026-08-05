<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AuthorResource extends JsonResource
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
            'dob' => $this->dob?->format('Y-m-d'),
            'gender' => $this->gender,
            'email' => $this->email,
            'image_url' => $this->image,
            'follower_count' => $this->follower_count,
            'books_count' => $this->when($this->books_count !== null, $this->books_count),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

