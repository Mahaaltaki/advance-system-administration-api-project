<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Services\TaskService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Trait\ApiResponceTrait;
class TaskController extends Controller

{
    protected $taskService;
    use ApiResponceTrait;
    public function __construct(taskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tasks = $this->taskService->getAllTasks();
            return $this->successResponse($tasks, 'All tasks fetched successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error fetching tasks.');
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->storeTask($request->validated());
            return $this->successResponse($task, 'Task stored successfully.', 201);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error storing task.');
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $task = $this->taskService->showTask($id);
            return $this->successResponse($task, 'Task retrieved successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error retrieving task.');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $updatedTask = $this->taskService->updateTask($task, $request->validated());
            return $this->successResponse($updatedTask, 'Task updated successfully.', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFound('Task not found.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error updating task.');
        }
    }
   
/**
     * Remove the one object from storage.
     */
    public function destroy(string $id):JsonResponse
    {
         try {
            $this->taskService->deletetask($id);
            return $this->successResponse([], 'the task deleted successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, 'error with deleting the task');
        }
    }
    public function assignTask(Request $request, $taskId)
    {
        $userId = $request->input('user_id');

        try {
            $task = $this->taskService->assignTaskToUser($taskId, $userId);
            return $this->successResponse( [$task],'Task assigned successfully' ,200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    /**
     * Update the status of a task.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        try {
            $status = $request->input('status');
            $updatedTask = $this->taskService->updateTaskStatus($id, $status);
            return $this->successResponse($updatedTask, 'Task status updated successfully.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'Error updating task status.');
        }
    }
        //reassign user to complete task
        public function reassignUser(Task  $task,string $userId)
        {
            $taskId = $task->id;
    
            try {
                $result = $this->taskService->reassignUserToTask($taskId, $userId);
                return response()->json($result, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        public function addComment(Request $request, $taskId)
        {
            $commentText = $request->input('comment');
            $userId = $request->input('user_id'); // يمكن تضمين user_id إذا كنت تتبع المستخدمين
    
            try {
                $comment = $this->taskService->addCommentToTask($taskId, $commentText);
                return $this->successResponse( [$comment],'Comment added successfully');
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }
        //add attachment to task
        public function addAttachment(Request $request, $taskId): JsonResponse
        {
            try {
                $attachment = $this->taskService->addAttachmentToTask($taskId, $request->file('attachment'));
                return $this->successResponse($attachment, 'Attachment added successfully.');
            } catch (\Exception $e) {
                return $this->handleException($e, 'Error adding attachment.');
            }
        }

    /**
     * Handle exceptions and show a response.
     */
    protected function handleException(\Exception $e, string $message): JsonResponse
    {
        // Log the error with additional context if needed
        Log::error($message, ['exception' => $e->getMessage(), 'request' => request()->all()]);

        return $this->errorResponse($message, [$e->getMessage()], 500);
    }
    public function getUsersWithTasks(Request $request): JsonResponse
    {
        try {
            $tasks = $this->taskService->getUsersWithTasks();
            return $this->successResponse($tasks, 'bring all tasks successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, ' error with bring all tasks.');
        }
    }

}



