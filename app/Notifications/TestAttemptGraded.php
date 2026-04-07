<?php

namespace App\Notifications;

use App\Models\TestAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestAttemptGraded extends Notification
{
    use Queueable;

    protected $attempt;

    public function __construct(TestAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'test_graded',
            'title' => 'Test Graded',
            'message' => "Your results for '{$this->attempt->mockTest->title}' are now available. Band: {$this->attempt->placeholder_band}.",
            'attempt_id' => $this->attempt->id,
            'link' => route('student.history', [], false),
        ];
    }
}
