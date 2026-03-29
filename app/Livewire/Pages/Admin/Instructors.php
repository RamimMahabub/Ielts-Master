<?php

namespace App\Livewire\Pages\Admin;

use App\Models\TestAttempt;
use App\Models\User;
use Livewire\Component;

class Instructors extends Component
{
    public $search = '';
    public $instructors;
    public $totalInstructors = 0;
    public $approvedInstructors = 0;
    public $pendingInstructors = 0;
    public $avgCreatedTests = 0;

    public $showDetailsModal = false;
    public $selectedInstructor;

    public function mount(): void
    {
        $this->loadInstructors();
    }

    public function updatedSearch(): void
    {
        $this->loadInstructors();
    }

    public function loadInstructors(): void
    {
        $records = User::role('instructor')
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount([
                'createdMockTests as total_tests_created',
                'createdMockTests as published_tests_count' => fn ($q) => $q->where('is_published', true),
            ])
            ->withMax('createdMockTests as last_test_created_at', 'created_at')
            ->orderBy('name')
            ->get();

        $this->instructors = $records->map(function ($instructor) {
            $instructor->pending_reviews_count = TestAttempt::where('status', 'pending_evaluation')
                ->whereHas('mockTest', fn ($q) => $q->where('created_by', $instructor->id))
                ->count();

            $instructor->effort_score = $this->computeEffortScore(
                (int) $instructor->total_tests_created,
                (int) $instructor->published_tests_count,
                $instructor->last_test_created_at
            );
            $instructor->effort_level = $this->effortLevel($instructor->effort_score);
            return $instructor;
        });

        $this->totalInstructors = User::role('instructor')->count();
        $this->approvedInstructors = User::role('instructor')->where('instructor_status', 'approved')->count();
        $this->pendingInstructors = User::role('instructor')->where('instructor_status', 'pending')->count();

        $totalCreated = User::role('instructor')->withCount('createdMockTests')->get()->sum('created_mock_tests_count');
        $this->avgCreatedTests = $this->totalInstructors > 0 ? round($totalCreated / $this->totalInstructors, 1) : 0;
    }

    public function viewInstructor(int $instructorId): void
    {
        $this->selectedInstructor = User::role('instructor')
            ->withCount([
                'createdMockTests as total_tests_created',
                'createdMockTests as published_tests_count' => fn ($q) => $q->where('is_published', true),
            ])
            ->with([
                'createdMockTests' => fn ($q) => $q->latest()->take(8),
            ])
            ->findOrFail($instructorId);

        $this->selectedInstructor->pending_reviews_count = TestAttempt::where('status', 'pending_evaluation')
            ->whereHas('mockTest', fn ($q) => $q->where('created_by', $instructorId))
            ->count();

        $this->selectedInstructor->effort_score = $this->computeEffortScore(
            (int) $this->selectedInstructor->total_tests_created,
            (int) $this->selectedInstructor->published_tests_count,
            optional($this->selectedInstructor->createdMockTests->first())->created_at
        );
        $this->selectedInstructor->effort_level = $this->effortLevel($this->selectedInstructor->effort_score);

        $this->showDetailsModal = true;
    }

    private function computeEffortScore(int $createdTests, int $publishedTests, $lastCreatedAt): int
    {
        $creationScore = min($createdTests * 10, 60);
        $publishRatio = $createdTests > 0 ? $publishedTests / $createdTests : 0;
        $qualityScore = (int) round($publishRatio * 25);

        $freshnessScore = 0;
        if ($lastCreatedAt) {
            $days = now()->diffInDays($lastCreatedAt);
            if ($days <= 14) {
                $freshnessScore = 15;
            } elseif ($days <= 45) {
                $freshnessScore = 8;
            } else {
                $freshnessScore = 3;
            }
        }

        return max(0, min(100, $creationScore + $qualityScore + $freshnessScore));
    }

    private function effortLevel(int $score): string
    {
        if ($score >= 75) {
            return 'High';
        }

        if ($score >= 45) {
            return 'Medium';
        }

        return 'Low';
    }

    public function render()
    {
        return view('livewire.pages.admin.instructors')->layout('layouts.app');
    }
}
