<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\ProfileApplicationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\TaskApplicationController;
use App\Http\Controllers\Api\TaskController;
use App\Models\ProfileApplication;
use App\Models\Review;
use App\Models\TaskApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// AUTH
Route::post('auth/login', [LoginController::class, 'authenticate']);
Route::post('auth/register', [RegisterController::class, 'authenticate']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/user', function (Request $request) {
        return $request->user();
    });
});

//PROFILES
Route::get('profiles', [ProfileController::class, 'index']);

//TASKS
Route::controller(TaskController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('tasks', 'getUserTasks');

    Route::patch('tasks/{task}','updateUserTaskStatus');
});

//REVIEWS
Route::controller(ReviewController::class)->group(function () {
    Route::get('profile/{profile}/reviews', 'getProfileReviews');

    Route::post('profile/{profile}/reviews', 'store')
        ->middleware('auth:sanctum');
});

//PROFILE APPLICATIONS
Route::controller(ProfileApplicationController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('profile-applications', 'index');
    Route::get('profile-applications/{application}', 'show');

    Route::patch('profile-applications/{application}/accept', 'accept');
    Route::patch('profile-applications/{application}/reject', 'reject');
});

Route::controller(TaskApplicationController::class)->middleware('auth:sanctum')->group(function () {
    Route::get('task-applications', 'getUserTaskApplications');

    Route::post('task-applications', 'createTaskApplication');

    Route::patch('task-applications/{application}/accept', 'acceptTaskApplication');
    Route::patch('task-applications/{application}/reject', 'rejectTaskApplication');
});
