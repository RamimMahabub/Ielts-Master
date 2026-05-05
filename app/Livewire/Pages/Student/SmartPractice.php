<?php

namespace App\Livewire\Pages\Student;

use App\Models\GuidedPracticeVideo;
use App\Models\LearningResource;
use App\Support\StudentSmartFeatures;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SmartPractice extends Component
{
    public array $weaknessReport = [];
    public array $guidedPracticeModules = [];
    public array $weaknessHeatmap = [];
    public array $microTasks = [];
    public array $recoveryPlan = [];
    public array $studyPlan = [];
    public $guidedResources;
    public $guidedVideos;

    public function mount(): void
    {
        $userId = Auth::id();

        $this->weaknessReport = StudentSmartFeatures::weaknessReport($userId);
        $this->guidedPracticeModules = $this->guidedPracticeModules();
        $this->weaknessHeatmap = $this->buildWeaknessHeatmap();
        $this->microTasks = $this->buildMicroTasks();
        $this->recoveryPlan = $this->buildRecoveryPlan();
        $this->studyPlan = $this->buildStudyPlan();
        $this->guidedResources = LearningResource::whereIn('category', $this->guidedPracticeModules)
            ->with('creator:id,name')
            ->orderByRaw("CASE category WHEN 'listening' THEN 1 WHEN 'reading' THEN 2 WHEN 'writing' THEN 3 WHEN 'speaking' THEN 4 ELSE 5 END")
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();
        $this->guidedVideos = GuidedPracticeVideo::whereIn('category', $this->guidedPracticeModules)
            ->with('creator:id,name')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.student.smart-practice')->layout('layouts.app');
    }

    private function guidedPracticeModules(): array
    {
        $performance = StudentSmartFeatures::performanceDashboard(Auth::id());

        return collect($performance['section_averages'] ?? [])
            ->filter(fn ($score) => $score !== null && (float) $score > 0 && (float) $score <= 4.0)
            ->sort()
            ->keys()
            ->values()
            ->all();
    }

    private function buildStudyPlan(): array
    {
        $modules = $this->guidedPracticeModules;

        if (empty($modules)) {
            $modules = collect($this->weaknessReport['weakest_modules'] ?? [])
                ->pluck('module')
                ->filter()
                ->values()
                ->all();
        }

        return collect($modules)
            ->take(3)
            ->map(fn (string $module) => [
                'module' => $module,
                'label' => ucfirst($module),
                'steps' => $this->modulePlanSteps($module),
            ])
            ->values()
            ->all();
    }

    private function buildWeaknessHeatmap(): array
    {
        $performance = StudentSmartFeatures::performanceDashboard(Auth::id());

        return collect($performance['section_averages'] ?? [])
            ->map(fn ($score, string $module) => [
                'module' => $module,
                'label' => ucfirst($module),
                'score' => $score ? (float) $score : null,
                'level' => $this->heatLevel($score ? (float) $score : null),
            ])
            ->values()
            ->all();
    }

    private function heatLevel(?float $score): array
    {
        if ($score === null || $score <= 0) {
            return [
                'label' => 'No data',
                'class' => 'border-slate-200 bg-slate-50 text-slate-600 dark:border-slate-700 dark:bg-slate-800/50 dark:text-slate-300',
            ];
        }

        if ($score <= 4.0) {
            return [
                'label' => 'Urgent',
                'class' => 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/50 dark:bg-rose-950/30 dark:text-rose-100',
            ];
        }

        if ($score <= 6.0) {
            return [
                'label' => 'Build',
                'class' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/30 dark:text-amber-100',
            ];
        }

        return [
            'label' => 'Stable',
            'class' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/30 dark:text-emerald-100',
        ];
    }

    private function buildMicroTasks(): array
    {
        $modules = $this->guidedPracticeModules ?: collect($this->weaknessReport['weakest_modules'] ?? [])
            ->pluck('module')
            ->filter()
            ->values()
            ->all();

        return collect($modules)
            ->flatMap(fn (string $module) => collect($this->moduleMicroTasks($module))->map(fn (string $task) => [
                'module' => ucfirst($module),
                'task' => $task,
            ]))
            ->take(6)
            ->values()
            ->all();
    }

    private function moduleMicroTasks(string $module): array
    {
        return match ($module) {
            'listening' => [
                'Write 10 predicted answer types before listening: number, date, name, place, verb.',
                'Replay 60 seconds of audio and list every synonym you hear for the question keywords.',
                'Check only spelling and plural/singular mistakes from your last attempt.',
            ],
            'reading' => [
                'Skim one passage in 90 seconds and write a 7-word summary.',
                'Find 5 synonyms between questions and passage sentences.',
                'For 3 wrong answers, write the exact trap word that misled you.',
            ],
            'writing' => [
                'Write a 4-sentence paragraph with one clear topic sentence.',
                'Replace 5 repeated words with stronger IELTS-style vocabulary.',
                'Check one answer only for cohesion markers and paragraph purpose.',
            ],
            'speaking' => [
                'Record a 45-second Part 2 answer without stopping.',
                "Add one reason, one example, and one contrast phrase to yesterday's answer.",
                'Replay your recording and count long pauses over two seconds.',
            ],
            default => [
                'Practice one weak skill for 15 minutes.',
            ],
        };
    }

    private function buildRecoveryPlan(): array
    {
        $modules = $this->guidedPracticeModules ?: collect($this->weaknessReport['weakest_modules'] ?? [])
            ->pluck('module')
            ->filter()
            ->values()
            ->all();

        if (empty($modules)) {
            return [];
        }

        $days = [
            'Day 1' => 'Reset the basics',
            'Day 2' => 'Timed accuracy drill',
            'Day 3' => 'Guided Practice review',
            'Day 4' => 'Mistake pattern review',
            'Day 5' => 'Mini performance check',
            'Day 6' => 'Repeat the weakest task',
            'Day 7' => 'Mock-test readiness check',
        ];

        return collect($days)
            ->map(function (string $focus, string $day) use ($modules) {
                $dayNumber = (int) filter_var($day, FILTER_SANITIZE_NUMBER_INT);

                return [
                    'day' => $day,
                    'focus' => $focus,
                    'module' => ucfirst($modules[($dayNumber - 1) % count($modules)]),
                ];
            })
            ->values()
            ->all();
    }

    private function modulePlanSteps(string $module): array
    {
        return match ($module) {
            'listening' => [
                'Read questions first and underline keywords before playing audio.',
                'Listen once without pausing and write answers immediately.',
                'Replay with transcript or audio notes, then make a spelling/error list.',
            ],
            'reading' => [
                'Skim title, headings, and first/last lines before reading deeply.',
                'Answer under time pressure and mark the exact sentence that proves each answer.',
                'Review wrong answers by writing why the trap option looked attractive.',
            ],
            'writing' => [
                'Spend 5 minutes planning paragraph purpose and examples.',
                'Write one timed paragraph or task response section.',
                'Check task response, cohesion, vocabulary range, and grammar accuracy.',
            ],
            'speaking' => [
                'Record a 60-second answer to one familiar topic.',
                'Replay and add one reason, one example, and one linking phrase.',
                'Record again and compare fluency, pauses, and pronunciation.',
            ],
            default => [
                'Review the weakest skill area from your latest mock test.',
                'Practice one focused task for 20 minutes.',
                'Write down the mistakes you need to avoid next time.',
            ],
        };
    }
}
