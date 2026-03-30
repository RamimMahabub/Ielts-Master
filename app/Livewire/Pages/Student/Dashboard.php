<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\TestAttempt;
use App\Models\MockTest;

class Dashboard extends Component
{
    public $user;
    public $recentAttempts;
    public $averageScore = 0;
    public $availableTests;
    public $notifications;

    public function mount()
    {
        $this->user = Auth::user();
        $this->recentAttempts = TestAttempt::where('user_id', $this->user->id)
            ->with('mockTest')
            ->latest()
            ->take(5)
            ->get();

        $this->averageScore = TestAttempt::where('user_id', $this->user->id)
            ->avg('raw_score') ?? 0;

        $this->availableTests = MockTest::where('is_published', true)
            ->with('sections.items.asset')
            ->latest()
            ->get();

        $this->notifications = $this->user->unreadNotifications;
    }

    public function markAsRead(string $id): void
    {
        $notification = $this->user->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->notifications = $this->user->fresh()->unreadNotifications;
    }

    public function render()
    {
        return view('livewire.pages.student.dashboard')->layout('layouts.app');
    }
}
