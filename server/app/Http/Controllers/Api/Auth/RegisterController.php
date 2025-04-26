<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRoles;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;

class RegisterController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function authenticate(StoreUserRequest $request)
    {
        $user = User::make($request->validated());

        $user->role()->associate(UserRoles::CLIENT->value);
        $user->save();

        $token = $this->authService->createUserAccessToken($user, 'register');

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
