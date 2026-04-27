<?php

namespace App\Livewire\Pages\Student;

use App\Models\MockTest;
use App\Models\TestAttempt as Attempt;
use App\Models\TestAttemptAnswer;
use App\Services\BandScore;
use App\Support\IeltsTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TestAttempt extends Component
{
    use WithFileUploads;

    /** Uploaded speaking-test audio recording (set via @this.upload from the browser). */
    public $speakingRecording = null;
    public MockTest $mockTest;
    public Attempt $attempt;

    /** Currently active module: listening|reading|writing|speaking */
    public string $currentModule = 'listening';

    /** answers[questionId] => string (or array for multi-select) */
    public array $answers = [];

    /** UNIX timestamp at which the current module's timer ends. */
    public ?int $endsAtTimestamp = null;

    public ?string $lastSavedAt = null;

    public function mount(int $id): void
    {
        $this->mockTest = MockTest::with([
            'modules.items.bankItem.groups.questions',
        ])->where('is_published', true)->findOrFail($id);

        $this->attempt = Attempt::firstOrCreate(
            ['user_id' => Auth::id(), 'mock_test_id' => $this->mockTest->id, 'status' => 'in_progress'],
            ['current_module' => $this->mockTest->modules->first()->module ?? 'listening']
        );

        $this->currentModule = $this->attempt->current_module ?? 'listening';

        // pre-load saved answers
        foreach ($this->attempt->answers as $a) {
            $this->answers[$a->question_id] = $a->answer_json !== null ? $a->answer_json : (string) $a->answer_text;
        }

        $this->endsAtTimestamp = $this->computeEndsAt();
    }

    public function getCurrentModuleObjProperty()
    {
        return $this->mockTest->modules->firstWhere('module', $this->currentModule);
    }

    private function computeEndsAt(): ?int
    {
        $mod = $this->currentModuleObj;
        if (!$mod) return null;
        $started = $this->attempt->module_started_at ?? null;
        if (!$started) return null;
        return $started->timestamp + ((int) $mod->duration_minutes) * 60;
    }

    public function startModule(): void
    {
        if (!$this->attempt->started_at) {
            $this->attempt->started_at = now();
        }
        $this->attempt->current_module = $this->currentModule;
        $this->attempt->module_started_at = now();
        $this->attempt->save();
        $this->endsAtTimestamp = $this->computeEndsAt();
    }

    public function saveAnswer(int $questionId, $value): void
    {
        if ($this->attempt->status !== 'in_progress') return;

        $this->answers[$questionId] = $value;

        $payload = is_array($value)
            ? ['answer_json' => array_values($value), 'answer_text' => null]
            : ['answer_text' => (string) $value, 'answer_json' => null];

        TestAttemptAnswer::updateOrCreate(
            ['attempt_id' => $this->attempt->id, 'question_id' => $questionId],
            $payload
        );

        $this->lastSavedAt = now()->format('H:i:s');
    }

    /**
     * Persist the speaking-test recording uploaded by the browser.
     * Called by the Alpine speaking-player after MediaRecorder finishes.
     */
    public function saveSpeakingRecording(): void
    {
        if (!$this->speakingRecording) {
            $this->dispatch('speaking-upload-failed', message: 'No recording received.');
            return;
        }

        $disk = 'public';
        $dir  = 'ielts/speaking/' . $this->attempt->id;
        $ext  = $this->speakingRecording->getClientOriginalExtension() ?: 'webm';
        $name = 'recording-' . now()->format('Ymd-His') . '.' . $ext;

        // Delete old recording if any (re-takes)
        if ($this->attempt->speaking_audio_path) {
            Storage::disk($disk)->delete($this->attempt->speaking_audio_path);
        }

        $path = $this->speakingRecording->storeAs($dir, $name, $disk);

        $this->attempt->speaking_audio_path = $path;
        $this->attempt->save();

        $this->speakingRecording = null;

        $this->dispatch('speaking-upload-complete');
    }

    public function finishModule(): void
    {
        // Speaking module: require an audio recording before allowing submit
        if ($this->currentModule === 'speaking' && empty($this->attempt->speaking_audio_path)) {
            $this->dispatch('speaking-submit-blocked', message: 'Please complete the speaking test and let your recording finish uploading before submitting.');
            return;
        }

        $this->gradeModule($this->currentModule);

        $modules = $this->mockTest->modules->pluck('module')->all();
        $idx = array_search($this->currentModule, $modules, true);
        $next = ($idx !== false && isset($modules[$idx + 1])) ? $modules[$idx + 1] : null;

        if ($next) {
            $this->currentModule = $next;
            $this->attempt->current_module = $next;
            $this->attempt->module_started_at = null;
            $this->attempt->save();
            $this->endsAtTimestamp = null;
        } else {
            $this->finalizeAttempt();
            $this->redirect(route('student.history'), navigate: false);
        }
    }

    private function gradeModule(string $module): void
    {
        $modObj = $this->mockTest->modules->firstWhere('module', $module);
        if (!$modObj) return;

        $correct = 0;
        $total   = 0;

        foreach ($modObj->items as $item) {
            foreach ($item->bankItem->groups as $group) {
                $autoGradable = IeltsTypes::isAutoGradable($group->question_type);
                foreach ($group->questions as $q) {
                    if (!$autoGradable) continue;

                    $total++;
                    $given = $this->answers[$q->id] ?? null;
                    $isCorrect = $this->matchesAnswer($given, $q->correct_answers_json ?? []);

                    TestAttemptAnswer::updateOrCreate(
                        ['attempt_id' => $this->attempt->id, 'question_id' => $q->id],
                        [
                            'answer_text' => is_array($given) ? null : (string) ($given ?? ''),
                            'answer_json' => is_array($given) ? array_values($given) : null,
                            'is_correct'  => $isCorrect,
                            'score'       => $isCorrect ? (float) $q->points : 0,
                        ]
                    );

                    if ($isCorrect) $correct++;
                }
            }
        }

        if ($module === 'listening') {
            $this->attempt->listening_raw  = $correct;
            $this->attempt->listening_band = BandScore::listening($correct);
        } elseif ($module === 'reading') {
            $this->attempt->reading_raw  = $correct;
            $this->attempt->reading_band = $this->mockTest->test_type === 'general'
                ? BandScore::readingGeneral($correct)
                : BandScore::readingAcademic($correct);
        }
        $this->attempt->save();
    }

    private function matchesAnswer($given, array $accepted): bool
    {
        if (empty($accepted)) return false;

        $normalize = fn ($s) => strtolower(trim((string) $s));
        $acceptedSet = array_map($normalize, $accepted);

        if (is_array($given)) {
            $g = array_map($normalize, $given);
            sort($g);
            $a = $acceptedSet;
            sort($a);
            return $g === $a;
        }
        return in_array($normalize($given), $acceptedSet, true);
    }

    public function finalizeAttempt(): void
    {
        // Grade any not-yet-graded module
        foreach ($this->mockTest->modules as $m) {
            if (in_array($m->module, ['listening', 'reading'], true)) {
                $rawField = $m->module . '_raw';
                if ($this->attempt->{$rawField} === null) {
                    $this->gradeModule($m->module);
                }
            }
        }

        // Writing/Speaking — pending instructor evaluation
        $hasWritingOrSpeaking = $this->mockTest->modules
            ->whereIn('module', ['writing', 'speaking'])
            ->isNotEmpty();

        $this->attempt->overall_band = BandScore::overall(
            $this->attempt->listening_band,
            $this->attempt->reading_band,
            $this->attempt->writing_band,
            $this->attempt->speaking_band,
        );

        $this->attempt->status = $hasWritingOrSpeaking && $this->attempt->overall_band === null
            ? 'pending_evaluation'
            : 'completed';
        $this->attempt->completed_at = now();
        $this->attempt->save();
    }

    public function render()
    {
        return view('livewire.pages.student.test-attempt', [
            'storageDisk' => Storage::disk('public'),
        ])->layout('layouts.app');
    }
}
