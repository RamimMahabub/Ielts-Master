<?php

namespace App\Notifications;

use App\Models\MockTest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMockTestPublished extends Notification
{
    use Queueable;

    protected $mockTest;

    public function __construct(MockTest $mockTest)
    {
        $this->mockTest = $mockTest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'new_mock_test',
            'title' => 'New Mock Test Published',
            'message' => "A new mock test '{$this->mockTest->title}' is now available.",
            'mock_test_id' => $this->mockTest->id,
            'link' => route('student.test.attempt', $this->mockTest->id),
        ];
    }
}
