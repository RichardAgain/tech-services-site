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
        $application = TaskApplication::make([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'status' => ApplicationStatuses::PENDING->value,
        ]);
        
        $application
            ->requester()->associate($request->user())
            ->requested()->associate($request->get('requested_id'))
            ->tags()->attach($request->get('tags'))
            ->save();

        return response()->json([
            'message' => 'Task application created successfully',
            'data' => new TaskResource($application),
        ]);
    }

    public function acceptTaskApplication(Request $request, TaskApplication $application)
    {
        $task = Task::make([
            'title' => $application->title,
            'description' => $application->description,
            'status' => ApplicationStatuses::APPROVED->value,
        ]);

        $task
            ->requester()->associate($application->requester)
            ->requested()->associate($request->user()->id)
            ->tags()->attach($application->tags)
            ->save();

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
