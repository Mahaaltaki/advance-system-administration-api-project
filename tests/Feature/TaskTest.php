<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Http\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $taskService;

   
    protected function setUp(): void
    {
        parent::setUp();
        
        // تهيئة الخدمة لاستخدامها في الاختبارات
        $this->taskService = new TaskService();
    }

    /** test */
    public function it_can_store_a_task()
    {
        // إنشاء مستخدم لتعيين المهمة
        $user = User::factory()->create();
        $task = [
            'title' => 'Test Task',
            'description' => 'This is a test task description.',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addWeek(),
            'type' => 'feature',
            'assigned_to' => $user->id,
        ];
        $response=$this->getJson("api/tasks/{task}");

        $response->assertStatus(200);
    }

    /** test */
    public function it_can_show_a_task()
    {
        // create new task
        $task = Task::factory()->create();

        $response=$this->getJson("api/tasks/{$task->id}");

        $response->assertStatus(200);
        //$this->assertEquals($task->title, $result['title']);
        //$this->assertEquals($task->description, $result['description']);
    }

    /** test */
    public function it_updates_task_status_correctly()
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response=$this->getJson("api/tasks/{$task->id}/complete");

        $response->assertStatus(200);
    }

    /** test */
    public function it_throws_exception_when_task_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Task not found.');

        $response=$this->getJson("api/tasks/44444");

    }

    /** test */
    public function it_can_reassign_user_to_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['assigned_to' => null]);

        $response=$this->getJson("api/tasks/{$task}/{$user->id}");

        $response->assertStatus(200);
    }
}
