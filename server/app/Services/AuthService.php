<?php

namespace App\Services;

use App\Enums\UserRoles;
use App\Models\User;

class AuthService {

    public function createUserAccessToken(User $user, $name='token')
    {
        $abilities = [];

        if ($user->role->id == UserRoles::ADMIN->value) {
            $abilities = ['admin'];
        } else if ($user->role->id == UserRoles::OPERATOR->value) {
            $abilities = ['operator'];
        }

        return $user->createToken($name, $abilities)->plainTextToken;
    }
}