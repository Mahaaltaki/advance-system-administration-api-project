<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء أدوار (Roles)
        $managerRole = Role::factory()->create([
            'name' => 'manager',
            'description' => 'control of all',
        ]);

        $editorRole = Role::factory()->create([
            'name' => 'editor',
            'description' => 'control of permission',
        ]);

        $userRole = Role::factory()->create([
            'name' => 'user',
            'description' => 'no control',
        ]);

        // إنشاء 10 مستخدمين عاديين
        User::factory(10)->create();

        // إنشاء المستخدم المدير (Manager)
        $manager = User::factory()->create([
            'name' => 'manager',
            'email' => 'manager@manager.com',
            'password' => Hash::make('123456789'),
        ]);
        // ربط المستخدم المدير بالدور (Manager Role)
        $manager->roles()->attach($managerRole->id);

        // إنشاء المستخدم المحرر (Editor)
        $editor = User::factory()->create([
            'name' => 'editor',
            'email' => 'editor@editor.com',
            'password' => Hash::make('123456789'),
        ]);
        // ربط المستخدم المحرر بالدور (Editor Role)
        $editor->roles()->attach($editorRole->id);
    }
}
