<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\TestAttempt;
use App\Models\MockTest;
use App\Models\StudentClassRecordingStatus;
use App\Models\StudentLearningResourceStatus;
use App\Support\StudentSmartFeatures;

class Dashboard extends Component
{
    public $user;
    public $recentAttempts;
    public $averageScore = 0;
    public $availableTests;
    public $notifications;
    public $completedLearningResourcesCount = 0;
    public $completedRecordingsCount = 0;
    public $watchLaterRecordings;
    public array $performanceDashboard = [];
    public array $weaknessReport = [];
    public $smartRecommendations;

    public function mount()
    {
        $this->user = Auth::user();
        $this->recentAttempts = TestAttempt::where('user_id', $this->user->id)
            ->with('mockTest')
            ->latest()
            ->take(5)
            ->get();

        $this->averageScore = TestAttempt::where('user_id', $this->user->id)
            ->whereNotNull('overall_band')
            ->avg('overall_band') ?? 0;

        $this->availableTests = MockTest::where('is_published', true)
            ->with('modules')
            ->latest()
            ->get();

        $this->notifications = $this->user->unreadNotifications;

        $this->completedLearningResourcesCount = StudentLearningResourceStatus::where('user_id', $this->user->id)
            ->whereNotNull('completed_at')
            ->count();

        $this->completedRecordingsCount = StudentClassRecordingStatus::where('user_id', $this->user->id)
            ->where('status', 'completed')
            ->count();

        $this->watchLaterRecordings = StudentClassRecordingStatus::where('user_id', $this->user->id)
            ->where('status', 'watch_later')
            ->with('classRecording')
            ->latest('updated_at')
            ->take(5)
            ->get();

        $this->loadSmartFeatures();
    }

    public function markAsRead(string $id): void
    {
        $notification = $this->user->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->notifications = $this->user->fresh()->unreadNotifications;
    }

    public function refreshNotifications(): void
    {
        $this->notifications = $this->user->fresh()->unreadNotifications;
        $this->availableTests = MockTest::where('is_published', true)
            ->with('modules')
            ->latest()
            ->get();
    }

    private function loadSmartFeatures(): void
    {
        $this->performanceDashboard = StudentSmartFeatures::performanceDashboard($this->user->id);
        $this->weaknessReport = StudentSmartFeatures::weaknessReport($this->user->id);
        $this->smartRecommendations = StudentSmartFeatures::recommendations($this->user->id, 4);
    }

    public function render()
    {
        return view('livewire.pages.student.dashboard')->layout('layouts.app');
    }
}
