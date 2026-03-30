<?php

namespace App\Livewire\Pages\Student;

use App\Models\IeltsQuiz;
use App\Models\IeltsQuizAttempt;
use App\Models\IeltsTopic;
use App\Models\IeltsUserProgress;
use App\Models\IeltsWord;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Vocabulary extends Component
{
    public $topics = [];
    public $selectedTopicId;
    public $dailyLimit = 10;
    public $todaysWords = [];
    public $wordStatusMap = [];
    public $totalWords = 0;
    public $masteredWords = 0;
    public $learningWords = 0;

    public $quizStarted = false;
    public $quizFinished = false;
    public $quizScore = 0;
    public $quizIndex = 0;
    public $quizQuestions = [];
    public $selectedOption = null;
    public $weakWordIds = [];

    public function mount(): void
    {
        $this->topics = IeltsTopic::query()->orderBy('name')->get(['id', 'name'])->toArray();
        $this->selectedTopicId = $this->topics[0]['id'] ?? null;

        $this->loadProgress();
        $this->loadTodaysWords();
    }

    public function updatedSelectedTopicId(): void
    {
        $this->loadTodaysWords();
        $this->resetQuizState();
    }

    public function updatedDailyLimit(): void
    {
        $this->dailyLimit = (int) $this->dailyLimit;
        if (!in_array($this->dailyLimit, [10, 15, 20], true)) {
            $this->dailyLimit = 10;
        }

        $this->loadTodaysWords();
    }

    public function loadTodaysWords(): void
    {
        $this->totalWords = IeltsWord::count();
        if ($this->totalWords === 0 || !$this->selectedTopicId) {
            $this->todaysWords = [];
            return;
        }

        $topicWordCount = IeltsWord::where('topic_id', $this->selectedTopicId)->count();
        if ($topicWordCount === 0) {
            $this->todaysWords = [];
            return;
        }

        $user = Auth::user();
        $offset = (($user->id + now()->dayOfYear - 1) * $this->dailyLimit) % $topicWordCount;

        $firstBatch = IeltsWord::query()
            ->where('topic_id', $this->selectedTopicId)
            ->with('collocations')
            ->orderBy('id')
            ->skip($offset)
            ->take($this->dailyLimit)
            ->get();

        if ($firstBatch->count() < $this->dailyLimit) {
            $remaining = $this->dailyLimit - $firstBatch->count();
            $secondBatch = IeltsWord::query()
                ->where('topic_id', $this->selectedTopicId)
                ->with('collocations')
                ->orderBy('id')
                ->take($remaining)
                ->get();
            $firstBatch = $firstBatch->concat($secondBatch);
        }

        $this->todaysWords = $firstBatch->toArray();
    }

    public function loadProgress(): void
    {
        $userId = Auth::id();

        $progressRows = IeltsUserProgress::query()
            ->where('user_id', $userId)
            ->get(['word_id', 'status']);

        $this->wordStatusMap = $progressRows
            ->mapWithKeys(fn ($row) => [$row->word_id => $row->status])
            ->toArray();

        $this->masteredWords = $progressRows->where('status', 'mastered')->count();
        $this->learningWords = $progressRows->where('status', 'learning')->count();
    }

    public function setWordStatus(int $wordId, string $status): void
    {
        if (!in_array($status, ['new', 'learning', 'mastered'], true)) {
            return;
        }

        $userId = Auth::id();

        IeltsUserProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'word_id' => $wordId,
            ],
            [
                'status' => $status,
                'last_reviewed_at' => now(),
                'next_review_at' => now()->addDays($status === 'mastered' ? 4 : 1),
            ]
        );

        $this->loadProgress();
    }

    public function startSectionQuiz(): void
    {
        if (!$this->selectedTopicId) {
            return;
        }

        $this->quizQuestions = IeltsQuiz::query()
            ->where('topic_id', $this->selectedTopicId)
            ->inRandomOrder()
            ->limit(5)
            ->get()
            ->toArray();

        if (count($this->quizQuestions) === 0) {
            $this->resetQuizState();
            return;
        }

        $this->quizStarted = true;
        $this->quizFinished = false;
        $this->quizScore = 0;
        $this->quizIndex = 0;
        $this->selectedOption = null;
        $this->weakWordIds = [];
    }

    public function submitAnswer(): void
    {
        if (!$this->quizStarted || $this->quizFinished || !isset($this->quizQuestions[$this->quizIndex])) {
            return;
        }

        $current = $this->quizQuestions[$this->quizIndex];
        $isCorrect = $this->selectedOption === $current['correct_answer'];

        if ($isCorrect) {
            $this->quizScore++;
        } elseif (!empty($current['word_id'])) {
            $this->weakWordIds[] = (int) $current['word_id'];
        }

        if (!empty($current['word_id'])) {
            $this->setWordStatus((int) $current['word_id'], $isCorrect ? 'mastered' : 'learning');
        }

        $this->quizIndex++;
        $this->selectedOption = null;

        if ($this->quizIndex >= count($this->quizQuestions)) {
            $this->quizFinished = true;

            IeltsQuizAttempt::create([
                'user_id' => Auth::id(),
                'topic_id' => $this->selectedTopicId,
                'total_questions' => count($this->quizQuestions),
                'score' => $this->quizScore,
                'weak_word_ids' => array_values(array_unique($this->weakWordIds)),
                'attempted_at' => now(),
            ]);
        }
    }

    public function resetQuizState(): void
    {
        $this->quizStarted = false;
        $this->quizFinished = false;
        $this->quizQuestions = [];
        $this->quizScore = 0;
        $this->quizIndex = 0;
        $this->selectedOption = null;
        $this->weakWordIds = [];
    }

    public function render()
    {
        $currentQuestion = $this->quizQuestions[$this->quizIndex] ?? null;

        return view('livewire.pages.student.vocabulary', [
            'currentQuestion' => $currentQuestion,
        ])->layout('layouts.app');
    }
}
