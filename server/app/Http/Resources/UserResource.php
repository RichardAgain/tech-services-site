<?php

namespace App\Http\Resources;

use App\Enums\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'profile' => $this->when($this->role->id == UserRoles::OPERATOR->value, function () {
                return new SimpleProfileResource($this->profile);
            }),
            'createdAt' => $this->created_at,
        ];
    }
}
