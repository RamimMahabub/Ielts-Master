<?php

namespace App\Livewire\Pages\Admin;

use App\Models\User;
use Livewire\Component;

class Students extends Component
{
    public $search = '';
    public $students;
    public $totalStudents = 0;
    public $activeThisWeek = 0;
    public $avgAttempts = 0;

    public $showDetailsModal = false;
    public $selectedStudent;

    public function mount(): void
    {
        $this->loadStudents();
    }

    public function updatedSearch(): void
    {
        $this->loadStudents();
    }

    public function loadStudents(): void
    {
        $records = User::role('student')
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount([
                'attempts as total_attempts',
                'attempts as completed_attempts' => fn ($q) => $q->whereIn('status', ['completed', 'pending_evaluation']),
                'attempts as pending_evaluation_attempts' => fn ($q) => $q->where('status', 'pending_evaluation'),
            ])
            ->withAvg('attempts as avg_raw_score', 'raw_score')
            ->withMax('attempts as last_attempt_at', 'created_at')
            ->orderBy('name')
            ->get();

        $this->students = $records->map(function ($student) {
            $student->effort_score = $this->computeEffortScore(
                (int) $student->total_attempts,
                (int) $student->completed_attempts,
                $student->last_attempt_at
            );
            $student->effort_level = $this->effortLevel($student->effort_score);
            $student->avg_raw_score = round((float) ($student->avg_raw_score ?? 0), 2);
            return $student;
        });

        $this->totalStudents = User::role('student')->count();
        $this->activeThisWeek = User::role('student')
            ->whereHas('attempts', fn ($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->count();

        $allAttemptCount = User::role('student')->withCount('attempts')->get()->sum('attempts_count');
        $this->avgAttempts = $this->totalStudents > 0 ? round($allAttemptCount / $this->totalStudents, 1) : 0;
    }

    public function viewStudent(int $studentId): void
    {
        $this->selectedStudent = User::role('student')
            ->withCount([
                'attempts as total_attempts',
                'attempts as completed_attempts' => fn ($q) => $q->whereIn('status', ['completed', 'pending_evaluation']),
                'attempts as pending_evaluation_attempts' => fn ($q) => $q->where('status', 'pending_evaluation'),
            ])
            ->withAvg('attempts as avg_raw_score', 'raw_score')
            ->with([
                'attempts' => fn ($q) => $q->with('mockTest')->latest()->take(8),
            ])
            ->findOrFail($studentId);

        $this->selectedStudent->effort_score = $this->computeEffortScore(
            (int) $this->selectedStudent->total_attempts,
            (int) $this->selectedStudent->completed_attempts,
            optional($this->selectedStudent->attempts->first())->created_at
        );
        $this->selectedStudent->effort_level = $this->effortLevel($this->selectedStudent->effort_score);

        $this->showDetailsModal = true;
    }

    private function computeEffortScore(int $totalAttempts, int $completedAttempts, $lastAttemptAt): int
    {
        $volumeScore = min($totalAttempts * 8, 60);
        $completionRate = $totalAttempts > 0 ? $completedAttempts / $totalAttempts : 0;
        $consistencyScore = (int) round($completionRate * 20);

        $freshnessScore = 0;
        if ($lastAttemptAt) {
            $days = now()->diffInDays($lastAttemptAt);
            if ($days <= 7) {
                $freshnessScore = 20;
            } elseif ($days <= 30) {
                $freshnessScore = 10;
            } else {
                $freshnessScore = 4;
            }
        }

        return max(0, min(100, $volumeScore + $consistencyScore + $freshnessScore));
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
        return view('livewire.pages.admin.students')->layout('layouts.app');
    }
}
