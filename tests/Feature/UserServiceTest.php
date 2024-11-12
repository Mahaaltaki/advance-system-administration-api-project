<?php

namespace Tests\Feature;

use Database\Factories\RoleFactory;
use Mockery;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserServiceTest extends TestCase
{


    protected $userService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // تهيئة الخدمة لاستخدامها في الاختبارات
        $this->userService = new UserService();
    }

    public function testGetAllUsers()
    {
        // قم بإنشاء عدة مستخدمين لاختبار التصفح
        User::factory()->count(15)->create();

        $request = new Request();
        $users = $this->userService->getAllUsers($request);

        // تحقق من أن عدد النتائج هو 10 (الصفحة الأولى)
        $this->assertCount(10, $users);
    }

    public function testStoreUser()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ];

        $user = $this->userService->storeUser($data);

        // تحقق من أن المستخدم تم إنشاؤه
        $this->assertEquals('John Doe', $user['name']);
        $this->assertEquals('john@example.com', $user['email']);
    }

    public function testShowUser()
    {
      // $id=20;
       $user=User::factory()->create();
       //$result = $this->userService->showUser($user->id);

    //    // تحقق من البيانات المسترجعة
        $response= $this->actingAs($user)->getJson("api/users/{$user->id}");
        $response->assertStatus(200);
    
    
    }

    public function testUpdateUser()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => bcrypt('newpassword')
        ];

        $updatedUser = $this->userService->updateUser($user, $data);

        // تحقق من أن المعلومات قد تم تحديثها
        $this->assertEquals('Jane Doe', $updatedUser['name']);
        $this->assertEquals('jane@example.com', $updatedUser['email']);
    }

    public function testDeleteUser()
    {
        $user = User::factory()->create();

        $this->userService->deleteUser($user->id);

        // تحقق من أن المستخدم تم حذفه
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testAddRoleToUser()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Editor']);
        $roles = [$role->id];
    
        $this->userService->addRoleToUser($user->id, $roles);
    
        $this->assertTrue($user->roles()->where('roles.id', $role->id)->exists());
    }
    
}
