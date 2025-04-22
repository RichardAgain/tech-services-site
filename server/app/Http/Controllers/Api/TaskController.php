<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApplicationStatuses;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function getUserTasks (Request $request)
    {
        $tasks = $request->user()->tasks;

        return TaskResource::collection($tasks);
    }

    public function updateUserTaskStatus (Request $request, Task $task)
    {
        $task->update(['status' => $request->get('status')]);

        return response()->json([
            'message' => 'Task updated successfully',
            'data' => $task,
        ]);
    }
}
