<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRoles;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileApplicationResource;
use App\Models\ProfileApplication;
use Illuminate\Http\Request;

class ProfileApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = ProfileApplication::orderBy('created_at', 'desc')->get();

        return ProfileApplication::collection($applications);
    }

    public function show(ProfileApplication $application)
    {
        return new ProfileApplicationResource($application);
    }

    public function accept(ProfileApplication $application)
    {
        $application->update(['status' => 'accepted']);

        $application->user->profile()->create([
            'description' => $application->description,
        ])->tags()->attach($application->tags);

        $application->user()->role()->associate(UserRoles::OPERATOR->value);

        return response()->json(['message' => 'Application accepted successfully.']);
    }
    
    public function reject(ProfileApplication $application)
    {
        $application->update(['status' => 'rejected']);

        return response()->json(['message' => 'Application rejected successfully.']);
    }
}
