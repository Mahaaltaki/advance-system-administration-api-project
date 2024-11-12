<?php
namespace App\Http\Services;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Jobs\SendEmailJob;
use App\Models\Attachment;
use App\Models\TaskDependency;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Services\AttachmentService;

class TaskService
{ protected $attachmentService;
    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;

    } 
    /*
     * @param Request $request 
     * @return array containing paginated task resources.
     */
    public function getAllTasks(): array
    {
        // query builder instance for the task model
        $query = Task::with('attachments');
        // Paginate the results
        $tasks = $query->paginate(10);

        // Return the paginated tasks wrapped in a taskResource collection
        return TaskResource::collection($tasks)->toArray(request());
    }

    /**
     * Store a new task.
     * @param array $data array containing 'title','description','status', 'priority',
      *  'due_date','project_id'
     * @return array array containing the created task resource.
     * @throws \Exception
     * Throws an exception if the task creation fails */
    public function storeTask(TaskRequest $request,array $data ): array
    {//$data = $request->validated();

        // Create a new task
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'type'=> $data['type'],
            'assigned_to' =>$data['assigned_to'],
        ]);
        $task->save();
        // if the task was created successfully
        if (!$task) {
            throw new \Exception('Failed to create task.');
        }
        // التحقق إذا كان هناك ملف مرفق
    if ($request->hasFile('file')) {
        // استدعاء خدمة تخزين الملف
        $file = $this->attachmentService->storefile($request);
        
        // ربط الملف بالمهمة الجديدة
        $task->attachments()->attach($file->id);
    }
        // Return the created task as a resource
        return taskResource::make($task)->toArray(request());
    }

    /*Retrieve a specific task by its ID.
     * @param int $id of the task.
     * @return array containing the task resource.
     * @throws \Exception exception if the task is not found.*/
    public function showTask(int $id): array
    {
        // Find task by ID
        $task = Task::find($id);
        // If task is not found, throw an exception
        if (!$task) {
            throw new \Exception('task not found.');
        }

        // Return the found task
        return taskResource::make($task)->toArray(request());
    }

    /**
     * Update an task.
     * @param Task $task
     * update The task model.
     * @param array $data array containing the fields to update ('title','description','status', 'priority',
      *  'due_date','project_id').
     * @return array containing the updated task resource.
     */
    public function updateTask(Task $task,  $data): array
    {
        // Update only the fields that are provided in the data array
        $task->update(array_filter([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'status' => $data['status'] ?? $task->status,
            'priority' => $data['priority'] ?? $task->priority,
            'due_date' => $data['due_date'] ?? $task->due_date,
            'type'=> $data['type']?? $task->type,
            'assigned_to' =>$data['assigned_to']?? $task->assigned_to,
        ]));
        $task->save();
        // Return the updated task as a resource
        return taskResource::make($task)->toArray(request());
    }

    /**
     * Delete task by ID.
     * @param int $id of task to delete.
     * @return void
     * @throws \Exception an exception if the task is not found.
     */
    public function deletetask(int $id): void
    {
        // Find the task by ID
        $task = Task::find($id);

        // If no task is found, throw an exception
        if (!$task) {
            throw new \Exception('task not found.');
        }

        // Delete task
        $task->delete();
    }
     /**
     * Update the status of a task.
     *
     * @param int $id
     * @param string $status
     * @return array
     * @throws \Exception
     */
    public function updateTaskStatus(int $id, string $status){
        $task=Task::find($id);
    if(! $task){
        throw new \Exception("the task not found");
    }
    $task->status = $status;
    $task->save(); // save changes
    // return update data
    // تسجيل الحالة الجديدة في تاريخ الحالات
    $task->taskStatusUpdate()->create([
        'status' => $status,
    ]);
     // to check dependent tasks
     if ($status == 'completed') {
        // bring in dependent tasks that depend on this task
        $dependentTasks = TaskDependency::where('task_id', $id)->get();

        foreach ($dependentTasks as $dependency) {
            $dependentTask = Task::find($dependency->dependent_task_id);

            // check if the tasks which dependent that has been completed
            $allCompleted = $dependentTask->dependencies->every(function ($dep) {
                return $dep->task->status === 'completed';
            });

            // if the tasks is completed ,change the status of task
            if ($allCompleted) {
                $dependentTask->status = 'in-progress';
            } else {
                $dependentTask->status = 'blocked';
            }

            $dependentTask->save();
        }
    }

    // return new data 
    return [
        'id' => $task->id,
        'status' => $task->status,
        'updated_at' => $task->updated_at,
    ];
    }
     /**
     * إعادة تعيين مستخدم لإتمام المهمة
     * @param int $taskId
     * @param int $userId
     * @return array
     * @throws \Exception
     */
    public function reassignUserToTask(int $taskId, int $userId): array
    {
        // get the task
        $task = Task::find($taskId);

        if (!$task) {
            throw new \Exception('Task not found.');
        }

        // get the user 
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        // check of the users roles 
        $userRoles = $user->roles; // Assumes User model has a 'roles' relationship

        // check the permission of user throw the roles of the users
        $hasPermission = false;

        foreach ($userRoles as $role) {
            // get the permission of the role
            $permissions = $role->permissions; // Assumes Role model has a 'permissions' relationship

            // check if the user have permission to complete this task
            foreach ($permissions as $permission) {
                if ($permission->name === 'complete_task') {
                    $hasPermission = true;
                    break 2;
                }
            }
        }

        if (!$hasPermission) {
            throw new \Exception('User does not have permission to complete tasks.');
        }

        // إعادة تعيين المستخدم لإتمام المهمة
        $task->assigned_to = $user->id;
        $task->save();

        return [
            'task_id' => $task->id,
            'assigned_to' => $user->id,
            'assigned_user_name' => $user->name,
            'message' => 'User has been reassigned to complete the task.'
        ];
    }
    /**
     * add comment to task
     * @param int $taskId
     * @param string $commentText
     * @return Comment
     * @throws \Exception
     */
    public function addCommentToTask(int $taskId, string $commentText)
    {
        // get the task
        $task = Task::find($taskId);

        if (!$task) {
            throw new \Exception('Task not found.');
        }

        // create comment 
        $comment = new Comment();
        $comment->text = $commentText;
        $comment->user_id = Auth::user()->id; //assign the user

        // link the comment with user
        $task->comments()->save($comment);

        return $comment;
    }
    public function addAttachmentTotask(int $taskId,Attachment $att){
        // get the task
        $task = Task::find($taskId);

        if (!$task) {
            throw new \Exception('Task not found.');
        }
        //add attachment
        $attachment=new Attachment();
        $attachment->file=$att->file;
       // link the comment with user
       $task->attachments()->save($attachment);

       return $attachment;
    }
    
    /**
     * تعيين مهمة لمستخدم
     * @param int $taskId
     * @param int $userId
     * @return Task
     * @throws \Exception
     */
    public function assignTaskToUser(int $taskId, int $userId): Task
    {
        // get task
        $task = Task::find($taskId);

        if (!$task) {
            throw new \Exception('Task not found.');
        }

        // get user
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception('User not found.');
        }

        // assign task to user
        $task->assigned_to = $user->id;
        $task->save();

        return $task;
    }
    // تحديث حالة المهمة والتحقق من التبعيات
    // public function updateTaskStatusDependOnOther(int $taskId, string $status)
    // {
    //     // get the task
    //     $task = Task::find($taskId);

    //     if (!$task) {
    //         throw new \Exception('المهمة غير موجودة');
    //     }

    //     // update status of task
    //     $task->status = $status;
    //     $task->save();

       
    // }
}
