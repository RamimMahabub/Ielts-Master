<?php

namespace App\Support;

use App\Models\MockTest;
use App\Models\User;
use App\Notifications\NewMockTestPublished;
use Illuminate\Support\Facades\Notification;

class StudentNotificationDispatcher
{
    public static function mockTestPublished(MockTest $mockTest): void
    {
        $students = User::role('student')->get();

        if ($students->isEmpty()) {
            return;
        }

        Notification::send($students, new NewMockTestPublished($mockTest));
    }
}
