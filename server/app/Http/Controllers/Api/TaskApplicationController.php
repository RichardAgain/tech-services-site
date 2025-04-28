<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatuses;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskApplicationRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskApplication;
use Illuminate\Http\Request;

class TaskApplicationController extends Controller
{
    public function getUserTaskApplications(Request $request)
    {
        $applications = $request->user()->taskApplications;

        return TaskResource::collection($applications);
    }

    public function createTaskApplication(StoreTaskApplicationRequest $request) //validate
    {
        $application = TaskApplication::make([
            ...$request->validated(),
            'requester_id' => $request->user()->id,
            'status' => ApplicationStatuses::PENDING->value,
        ]);

        $application->save();

        if (count($request->get('tags')) > 0) {
            $application->tags()->attach(1);
        }

        return response()->json([
            'message' => 'Task application created successfully',
            'data' => new TaskResource($application),
        ]);
    }

    public function acceptTaskApplication(Request $request, TaskApplication $application)
    {
        $task = Task::create([
            'requester_id' => $application->requester->id,
            'required_id' => $request->user()->id,
            'title' => $application->title,
            'description' => $application->description,
            'status' => ApplicationStatuses::APPROVED->value,
        ]);

        $application->update(['status' => ApplicationStatuses::APPROVED->value]);

        return response()->json([
            'message' => 'Task created successfully',
            'data' => new TaskResource($task),
        ]);
    }

    public function rejectTaskApplication(Request $request, TaskApplication $application)
    {
        $application->update(['status' => ApplicationStatuses::REJECTED->value]);

        return response()->json([
            'message' => 'Task application rejected',
            'data' => new TaskResource($application),
        ]);
    }
}
