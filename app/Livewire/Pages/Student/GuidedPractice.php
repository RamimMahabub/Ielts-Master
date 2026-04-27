<?php

namespace App\Livewire\Pages\Student;

use App\Models\ClassRecording;
use App\Models\GuidedPracticeVideo;
use App\Models\LearningResource;
use App\Models\StudentClassRecordingStatus;
use App\Models\StudentLearningResourceStatus;
use App\Support\IeltsTypes;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GuidedPractice extends Component
{
    public string $selectedCategory = 'listening';
    public array $completedResourceIds = [];
    public array $recordingStatuses = [];

    public function mount(): void
    {
        $this->refreshResourceStatuses();
        $this->refreshRecordingStatuses();
    }

    public function setCategory(string $category): void
    {
        if (!in_array($category, IeltsTypes::MODULES, true)) {
            return;
        }

        $this->selectedCategory = $category;
    }

    public function completeResource(int $resourceId): void
    {
        StudentLearningResourceStatus::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'learning_resource_id' => $resourceId,
            ],
            [
                'completed_at' => now(),
            ]
        );

        $this->refreshResourceStatuses();
        session()->flash('status', 'Marked as completed.');
    }

    public function markRecordingCompleted(int $recordingId): void
    {
        StudentClassRecordingStatus::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'class_recording_id' => $recordingId,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        $this->refreshRecordingStatuses();
        session()->flash('recording_status', 'Recording marked as completed.');
    }

    public function markRecordingWatchLater(int $recordingId): void
    {
        StudentClassRecordingStatus::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'class_recording_id' => $recordingId,
            ],
            [
                'status' => 'watch_later',
                'completed_at' => null,
            ]
        );

        $this->refreshRecordingStatuses();
        session()->flash('recording_status', 'Added to watch later.');
    }

    protected function refreshResourceStatuses(): void
    {
        $this->completedResourceIds = StudentLearningResourceStatus::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->pluck('learning_resource_id')
            ->toArray();
    }

    protected function refreshRecordingStatuses(): void
    {
        $this->recordingStatuses = StudentClassRecordingStatus::where('user_id', Auth::id())
            ->get()
            ->pluck('status', 'class_recording_id')
            ->toArray();
    }

    public function render()
    {
        $resources = LearningResource::where('category', $this->selectedCategory)
            ->with('creator:id,name')
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

        $recordings = ClassRecording::with('creator:id,name')
            ->latest()
            ->get();

        $categoryVideos = GuidedPracticeVideo::where('category', $this->selectedCategory)
            ->with('creator:id,name')
            ->latest()
            ->get();

        return view('livewire.pages.student.guided-practice', [
            'categories' => IeltsTypes::MODULES,
            'resources' => $resources,
            'recordings' => $recordings,
            'categoryVideos' => $categoryVideos,
        ])->layout('layouts.app');
    }
}
