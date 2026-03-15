<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use App\Models\MockTest;
use App\Models\TestAttemptAnswer;
use Illuminate\Support\Facades\Auth;

class TestAttempt extends Component
{
    public $mockTest;
    public $attempt;
    public $answers = [];
    public $timeLeft;
    public $endsAtTimestamp;
    public $lastSavedAt;

    public function mount($id)
    {
        $this->mockTest = MockTest::with('sections.items.asset.questionGroups.questions.answers')->findOrFail($id);

        $this->attempt = \App\Models\TestAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'mock_test_id' => $id, 'status' => 'in_progress'],
            ['raw_score' => 0]
        );

        $this->timeLeft = $this->remainingSeconds();
        $this->endsAtTimestamp = $this->attemptEndsAtTimestamp();

        if ($this->attempt->started_at && $this->timeLeft <= 0 && $this->attempt->status === 'in_progress') {
            $this->finalizeAttempt();
            return redirect()->route('student.dashboard');
        }

        // Pre-fill existing answers
        foreach ($this->attempt->answers as $ans) {
            $this->answers[$ans->question_id] = $ans->answer_text;
        }
    }

    public function startAttempt()
    {
        if (!$this->attempt->started_at) {
            $this->attempt->update([
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
            $this->attempt->refresh();
        }

        $this->timeLeft = $this->remainingSeconds();
        $this->endsAtTimestamp = $this->attemptEndsAtTimestamp();

        return [
            'started' => true,
            'secondsLeft' => $this->timeLeft,
            'endsAtTimestamp' => $this->endsAtTimestamp,
        ];
    }

    public function saveAnswer($questionId, $value)
    {
        if (!$this->attempt->started_at || $this->attempt->status !== 'in_progress') {
            return;
        }

        if ($this->remainingSeconds() <= 0) {
            $this->finalizeAttempt();
            return;
        }

        $this->answers[$questionId] = $value;

        TestAttemptAnswer::updateOrCreate(
            ['attempt_id' => $this->attempt->id, 'question_id' => $questionId],
            ['answer_text' => $value]
        );

        $this->lastSavedAt = now()->format('H:i:s');
    }

    public function submitTest()
    {
        if ($this->attempt->status !== 'in_progress') {
            return redirect()->route('student.dashboard');
        }

        if (!$this->attempt->started_at) {
            $this->attempt->update(['started_at' => now()]);
            $this->attempt->refresh();
        }

        $this->finalizeAttempt();

        return redirect()->route('student.dashboard');
    }

    private function finalizeAttempt(): void
    {
        if ($this->attempt->status !== 'in_progress') {
            return;
        }

        $totalAutoGradable = 0;
        $correctCount = 0;
        $requiresEvaluation = false;

        $questions = $this->mockTest->sections
            ->flatMap(fn($section) => $section->items)
            ->flatMap(fn($item) => $item->asset->questionGroups)
            ->flatMap(fn($group) => $group->questions->map(fn($question) => [
                'question' => $question,
                'question_type' => $group->question_type,
                'section_type' => $group->asset->type ?? '',
            ]));

        foreach ($questions as $row) {
            $question = $row['question'];
            $questionType = $row['question_type'];
            $answerText = trim((string) ($this->answers[$question->id] ?? ''));

            $autoGradable = in_array($questionType, ['short_answer', 'note_completion', 'multiple_choice'], true);
            if (!$autoGradable) {
                if ($answerText !== '') {
                    $requiresEvaluation = true;
                }
                continue;
            }

            $totalAutoGradable++;
            $correctAnswer = strtolower(trim((string) optional($question->answers->first())->answer_text));
            $isCorrect = $correctAnswer !== '' && strtolower($answerText) === $correctAnswer;

            TestAttemptAnswer::updateOrCreate(
                ['attempt_id' => $this->attempt->id, 'question_id' => $question->id],
                [
                    'answer_text' => $answerText,
                    'is_correct' => $isCorrect,
                    'score' => $isCorrect ? 1 : 0,
                ]
            );

            if ($isCorrect) {
                $correctCount++;
            }
        }

        $rawScore = $correctCount;
        $status = $requiresEvaluation ? 'pending_evaluation' : 'completed';

        $this->attempt->update([
            'status' => $status,
            'raw_score' => $rawScore,
            'placeholder_band' => '6.5',
            'completed_at' => now(),
        ]);
        $this->attempt->refresh();
    }

    private function remainingSeconds(): int
    {
        $durationSeconds = (int) $this->mockTest->duration_minutes * 60;

        if (!$this->attempt->started_at) {
            return $durationSeconds;
        }

        $elapsed = now()->timestamp - $this->attempt->started_at->timestamp;
        $elapsed = max($elapsed, 0);

        return max($durationSeconds - $elapsed, 0);
    }

    private function attemptEndsAtTimestamp(): ?int
    {
        if (!$this->attempt->started_at) {
            return null;
        }

        return $this->attempt->started_at->timestamp + ((int) $this->mockTest->duration_minutes * 60);
    }

    public function render()
    {
        return view('livewire.pages.student.test-attempt')->layout('layouts.app');
    }
}
