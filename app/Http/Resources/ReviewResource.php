<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'reviewer' => [
                'username' => $this->reviewer->name,
                'firtName' => $this->reviewer->firstName,
                'lastName' => $this->reviewer->lastName,
            ],
            'title' => $this->title,
            'content' => $this->content,
            'rating' => $this->rating,
            'created_at' => $this->created_at->format('d-m-Y'),
        ];
    }
}
