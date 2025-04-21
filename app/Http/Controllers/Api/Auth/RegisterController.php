<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function authenticate(StoreUserRequest $request)
    {
        $user = User::make($request->validated());

        $user->role()->associate(2);

        $user->save();

        return response()->json([
            'token' => $user->createToken('token')->plainTextToken
        ]);
    }
}
