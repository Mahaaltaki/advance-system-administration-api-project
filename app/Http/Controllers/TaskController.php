<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Services\taskService;
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
            return $this->successResponse($tasks, 'bring all tasks successfully.', 200);
        } catch (\Exception $e) {
            return $this->handleException($e, ' error with bring all tasks.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request ,array $data)
{
    try {
        $task = $this->taskService->storeTask($request,$data);
        return $this->successResponse($task, 'The task stored successfully', 201);
    } catch (\Exception $e) {
        return $this->handleException($e, 'Error storing the task');
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{
        
            $task=$this->taskService->showTask($id);
            return $this->successResponse($task,'the task has been showing successfuly',200);
        }catch(\Exception $e){
         return $this->handleException($e,'error with showing the task');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, $id)
    {
    try {
        $task = Task::findOrFail($id);
        $validated = $request->validated();
        $updatedTask = $this->taskService->updateTask($task, $validated);
        return $this->successResponse($updatedTask, 'The task updated successfully', 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return $this->notFound('The task not found');
    } catch (\Exception $e) {
        return $this->handleException($e, 'Error updating the task');
    }


    }
    public function update_assigned_to(TaskRequest $request, Task $task)
    {
        try{
            if(!$task->exists){
                return $this->notFound('the task not found');
            }
            $validated=$request->validated();
            $updatedTask=$this->taskService->update_assigned_to($task,$validated);
           return $this->successResponse($updatedTask,'the task updated successfuly',200);
        }catch(\Exception $e){
            return $this->handleException($e,'error with updating task');
    
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
            // get the status from $request
            $status = $request->input('status');

            // تحديث الحالة باستخدام TaskService
            $updatedTask = $this->taskService->updateTaskStatus($id, $status);

            return $this->successResponse($updatedTask, 'Task status updated successfully.');
        } catch (\Exception $e) {
            return $this->handleException($e, 'error with updating the task');
        }
        }
        //reassign user to complete task
        public function reassignUser(Request $request, $taskId)
        {
            $userId = $request->input('user_id');
    
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
        public function addAttachment(Request $request, $taskId)
        {
            $file = $request->input('file');
            $taskId = $request->input('task_id'); // يمكن تضمين user_id إذا كنت تتبع المستخدمين
    
            try {
                $file= $this->taskService->addCommentToTask($taskId, $file);
                return $this->successResponse( [$file],'Comment added successfully',200);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
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



