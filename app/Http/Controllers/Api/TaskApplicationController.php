<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatuses;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskApplication;
use Illuminate\Http\Request;

class TaskApplicationController extends Controller
{
    public function getUserTaskApplications(Request $request)
    {
        $applications = $request->user()->taskApplications;

        return response()->json($applications);
    }

    public function createTaskApplication(Request $request) //validate
    {
        $application = TaskApplication::create([
            'requester_id' => $request->user()->id,
            'requested_id' => $request->get('requested_id'),
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'status' => ApplicationStatuses::PENDING->value,

        ])->tags()->attach($request->get('tags'));

        return response()->json([
            'message' => 'Task application created successfully',
            'data' => new TaskResource($application),
        ]);
    }

    public function acceptTaskApplication(Request $request, TaskApplication $application)
    {
        $task = Task::create([
            'requester_id' => $application->requester_id,
            'required_id' => $request->user()->id,
            'title' => $application->title,
            'description' => $application->description,
            'status' => ApplicationStatuses::APPROVED->value,

        ])->tags()->attach($application->tags);

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
