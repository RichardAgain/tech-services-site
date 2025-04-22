<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Profile;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function getProfileReviews(Request $request, Profile $profile)
    {
        $reviews = $profile->reviews;

        return ReviewResource::collection($reviews);
    }

    public function create(StoreReviewRequest $request, Profile $profile)
    {
        $review = $profile->reviews()->create([
            'reviewer_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return new ReviewResource($review);
    }
}
