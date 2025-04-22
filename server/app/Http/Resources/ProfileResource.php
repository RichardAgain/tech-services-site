<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
            ],
            
            'firstName' => $this->user->firstName,
            'lastName' => $this->user->lastName,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'address' => $this->user->address,
            'description' => $this->description,
            'rating' => $this->rating,
            'verifiedAt' => $this->created_at,
            'tags' => $this->tags,
        ];
    }
}
