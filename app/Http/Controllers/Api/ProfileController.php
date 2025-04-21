<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Tag;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index (Request $request)
    {
        $tags = Tag::whereIn('id', explode(',', $request->query('tags')))->get();

        if ($tags->isEmpty()) {
            $profiles = Profile::whereAttachedTo($tags)->get();
        } else {
            $profiles = Profile::get();
        }

        $profiles->load(['user', 'tags']);

        return response()->json($profiles);
    }
}
