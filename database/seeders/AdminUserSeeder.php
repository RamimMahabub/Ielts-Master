<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'instructor_status' => 'approved',
                'is_blocked' => false,
            ]
        );
        $admin->syncRoles(['admin']);

        $instructor = User::updateOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'name' => 'Instructor User',
                'password' => 'password',
                'instructor_status' => 'approved',
                'is_blocked' => false,
            ]
        );
        $instructor->syncRoles(['instructor']);

        $student = User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'Student User',
                'password' => 'password',
                'target_band' => '7.0',
                'preferred_test_date' => now()->addMonth()->toDateString(),
                'instructor_status' => 'none',
                'is_blocked' => false,
            ]
        );
        $student->syncRoles(['student']);
    }
}
