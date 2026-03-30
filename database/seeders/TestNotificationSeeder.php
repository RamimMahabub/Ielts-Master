<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MockTest;
use App\Models\TestAttempt;
use App\Notifications\NewMockTestPublished;
use App\Notifications\TestAttemptGraded;

class TestNotificationSeeder extends Seeder
{
    public function run()
    {
        $user = User::find(3);
        if (!$user) {
            $user = User::create([
                'id' => 3,
                'name' => 'Student User',
                'email' => 'student@example.com',
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('student');
        }

        $test = MockTest::first();
        if (!$test) {
            $test = MockTest::create([
                'title' => 'Sample IELTS Academic Test',
                'duration_minutes' => 150,
                'is_published' => true,
                'created_by' => 1,
            ]);
        }

        $attempt = TestAttempt::firstOrCreate(
            ['user_id' => 3, 'mock_test_id' => $test->id],
            ['status' => 'completed', 'raw_score' => 32, 'placeholder_band' => '7.5']
        );

        // Clear old notifications first to see fresh ones
        $user->notifications()->delete();

        // Send new notifications
        $user->notify(new NewMockTestPublished($test));
        $user->notify(new TestAttemptGraded($attempt));
    }
}
