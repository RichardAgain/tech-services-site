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

    public function create(Request $request)
    {
        $application = ProfileApplication::create([
            'user_id' => $request->user()->id,
            'description' => $request->input('description'),
            'status' => 'pending',
        ]);

        if (count($request->input('tags')) > 0) {
            $application->tags()->attach($request->input('tags'));
        }

        return response()->json([
            'message' => 'Profile application created successfully',
            'data' => new ProfileApplicationResource($application),
        ]);
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
