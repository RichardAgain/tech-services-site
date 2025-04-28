<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\ReviewResource;
use App\Models\Profile;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index (Request $request)
    {
        $tags = Tag::whereIn('id', explode(',', $request->query('tags')))->get();

        if (!$tags->isEmpty()) {
            $profiles = Profile::whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('id', $tags->pluck('id'));
            }, '=', $tags->count())->get();
        } else {
            $profiles = Profile::get();
        }

        return [
            'profiles' => ProfileResource::collection($profiles),
            'tags' => Tag::all(),
        ];
    }

    public function show (Profile $profile)
    {
        return [
            'profile' => new ProfileResource($profile),
            'reviews' => ReviewResource::collection($profile->reviews),
        ]; 
    }
}
