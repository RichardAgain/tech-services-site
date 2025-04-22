<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'requester' => [
                'id' => $this->requester->id,
                'username' => $this->requester->username,
            ],
            'requested' => [
                'id' => $this->required->id,
                'username' => $this->required->username,
            ],
            'createdAt' => $this->created_at,
            'status' => $this->status,
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $this->tags,
        ];
    }
}
