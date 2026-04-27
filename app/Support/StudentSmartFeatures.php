<?php

namespace App\Support;

use App\Models\Question;
use App\Models\TestAttempt;
use Illuminate\Support\Collection;

class StudentSmartFeatures
{
    public static function performanceDashboard(int $userId): array
    {
        $attempts = TestAttempt::where('user_id', $userId)
            ->whereIn('status', ['completed', 'pending_evaluation'])
            ->with('mockTest')
            ->oldest()
            ->get();

        $sectionAverages = collect(IeltsTypes::MODULES)
            ->mapWithKeys(fn (string $module) => [
                $module => self::averageBand($attempts, $module . '_band'),
            ])
            ->all();

        $progress = $attempts
            ->whereNotNull('overall_band')
            ->take(-8)
            ->values()
            ->map(fn (TestAttempt $attempt, int $index) => [
                'label' => 'Test ' . ($index + 1),
                'test' => $attempt->mockTest->title ?? 'Mock Test',
                'band' => (float) $attempt->overall_band,
                'date' => optional($attempt->completed_at ?? $attempt->created_at)->format('M d'),
            ])
            ->all();

        return [
            'attempt_count' => $attempts->count(),
            'section_averages' => $sectionAverages,
            'overall_average' => self::averageBand($attempts, 'overall_band'),
            'progress' => $progress,
            'highest_overall' => (float) ($attempts->max('overall_band') ?? 0),
        ];
    }

    public static function weaknessReport(int $userId): array
    {
        $performance = self::performanceDashboard($userId);
        $sectionAverages = collect($performance['section_averages'])
            ->filter(fn (?float $score) => $score !== null && $score > 0);

        $weakestModules = $sectionAverages
            ->sort()
            ->take(2)
            ->map(fn (?float $score, string $module) => [
                'module' => $module,
                'label' => ucfirst($module),
                'score' => $score,
                'message' => self::moduleAdvice($module, $score),
            ])
            ->values();

        $questionTypeWeaknesses = self::incorrectAnswerStats($userId)
            ->sortByDesc('wrong')
            ->take(4)
            ->values()
            ->map(fn (array $row) => [
                ...$row,
                'message' => self::questionTypeAdvice($row['question_type']),
            ]);

        return [
            'weakest_modules' => $weakestModules->all(),
            'question_types' => $questionTypeWeaknesses->all(),
            'summary' => self::weaknessSummary($weakestModules, $questionTypeWeaknesses),
        ];
    }

    public static function recommendations(int $userId, int $limit = 8): Collection
    {
        $weaknesses = self::weaknessReport($userId);
        $weakModules = collect($weaknesses['weakest_modules'])->pluck('module')->all();
        $weakTypes = collect($weaknesses['question_types'])->pluck('question_type')->all();

        $query = Question::query()
            ->with(['group.item'])
            ->whereHas('group.item', function ($itemQuery) use ($weakModules) {
                if (!empty($weakModules)) {
                    $itemQuery->whereIn('module', $weakModules);
                }
            })
            ->when(!empty($weakTypes), fn ($q) => $q->whereHas('group', fn ($groupQuery) => $groupQuery->whereIn('question_type', $weakTypes)))
            ->whereDoesntHave('answers.attempt', fn ($q) => $q->where('user_id', $userId)->where('status', 'completed'))
            ->orderBy('q_number')
            ->limit($limit)
            ->get();

        if ($query->count() >= min(4, $limit)) {
            return $query;
        }

        return Question::query()
            ->with(['group.item'])
            ->whereHas('group.item', function ($itemQuery) use ($weakModules) {
                if (!empty($weakModules)) {
                    $itemQuery->whereIn('module', $weakModules);
                }
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    private static function averageBand(Collection $attempts, string $field): ?float
    {
        $values = $attempts
            ->pluck($field)
            ->filter(fn ($value) => $value !== null);

        if ($values->isEmpty()) {
            return null;
        }

        return round((float) $values->avg(), 1);
    }

    private static function incorrectAnswerStats(int $userId): Collection
    {
        $attempts = TestAttempt::where('user_id', $userId)
            ->whereIn('status', ['completed', 'pending_evaluation'])
            ->with('answers.question.group.item')
            ->latest()
            ->take(12)
            ->get();

        return $attempts
            ->flatMap->answers
            ->filter(fn ($answer) => $answer->is_correct === false && $answer->question?->group)
            ->groupBy(fn ($answer) => $answer->question->group->question_type)
            ->map(fn (Collection $answers, string $type) => [
                'question_type' => $type,
                'label' => str_replace('_', ' ', $type),
                'module' => $answers->first()->question->group->item->module ?? null,
                'wrong' => $answers->count(),
            ]);
    }

    private static function weaknessSummary(Collection $weakestModules, Collection $questionTypeWeaknesses): string
    {
        if ($weakestModules->isEmpty() && $questionTypeWeaknesses->isEmpty()) {
            return 'Complete more mock tests to unlock personalized weakness analysis.';
        }

        if ($weakestModules->isNotEmpty()) {
            return 'Your next focus should be ' . $weakestModules->pluck('label')->join(' and ') . '.';
        }

        return 'Review question formats where incorrect answers appear most often.';
    }

    private static function moduleAdvice(string $module, ?float $score): string
    {
        return match ($module) {
            'listening' => 'Practice identifying keywords before the audio and checking spelling after each answer.',
            'reading' => 'Prioritize skimming for structure, then scan for exact evidence before choosing an answer.',
            'writing' => 'Plan paragraph purpose first, then check task response, cohesion, vocabulary, and grammar.',
            'speaking' => 'Record short answers daily and expand with reasons, examples, and natural linking phrases.',
            default => 'Review recent mistakes and repeat focused practice sets.',
        };
    }

    private static function questionTypeAdvice(string $type): string
    {
        return match ($type) {
            'mcq_single', 'mcq_multi' => 'Underline distractors and eliminate choices that are only partially true.',
            'tfng', 'ynng' => 'Separate what the passage says from what seems generally true.',
            'matching_headings', 'matching_information', 'matching_features', 'matching_sentence_endings', 'matching' => 'Match by main idea first, then confirm with exact detail.',
            'summary_completion', 'sentence_completion', 'note_completion', 'table_completion', 'flow_chart_completion' => 'Predict the word form before looking for the answer.',
            'short_answer' => 'Keep answers brief and copy wording carefully from the source.',
            default => 'Redo this format slowly and write down why each wrong option is wrong.',
        };
    }
}
