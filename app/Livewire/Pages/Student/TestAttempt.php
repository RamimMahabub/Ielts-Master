<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use App\Models\MockTest;
use App\Models\TestAttempt as TestAttemptModel;
use App\Models\TestAttemptAnswer;
use Illuminate\Support\Facades\Auth;

class TestAttempt extends Component
{
    public $mockTest;
    public $attempt;
    public $answers = [];
    public $timeLeft;
    public $lastSavedAt;

    public function mount($id)
    {
        $this->mockTest = MockTest::with('sections.items.asset.questionGroups.questions')->findOrFail($id);

        $this->attempt = \App\Models\TestAttempt::firstOrCreate(
            ['user_id' => Auth::id(), 'mock_test_id' => $id, 'status' => 'in_progress'],
            ['raw_score' => 0]
        );

        $this->timeLeft = $this->mockTest->duration_minutes * 60;

        // Pre-fill existing answers
        foreach ($this->attempt->answers as $ans) {
            $this->answers[$ans->question_id] = $ans->answer_text;
        }
    }

    public function saveAnswer($questionId, $value)
    {
        $this->answers[$questionId] = $value;

        TestAttemptAnswer::updateOrCreate(
            ['attempt_id' => $this->attempt->id, 'question_id' => $questionId],
            ['answer_text' => $value]
        );

        $this->lastSavedAt = now()->format('H:i:s');
    }

    public function submitTest()
    {
        $totalAutoGradable = 0;
        $correctCount = 0;
        $requiresEvaluation = false;

        $questions = $this->mockTest->sections
            ->flatMap(fn ($section) => $section->items)
            ->flatMap(fn ($item) => $item->asset->questionGroups)
            ->flatMap(fn ($group) => $group->questions->map(fn ($question) => [
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
            'placeholder_band' => '6.5'
        ]);

        return redirect()->route('student.history');
    }

    public function render()
    {
        return view('livewire.pages.student.test-attempt')->layout('layouts.app');
    }
}
