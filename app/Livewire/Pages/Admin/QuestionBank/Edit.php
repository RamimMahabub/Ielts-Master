<?php

namespace App\Livewire\Pages\Admin\QuestionBank;

use App\Models\Question;
use App\Models\QuestionBankItem;
use App\Models\QuestionGroup;
use App\Support\IeltsTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public ?int $itemId = null;
    public string $module = 'reading';

    // Common fields
    public string $title = '';

    // Listening
    public $audio;                  // upload
    public ?string $audioPath = null;
    public string $transcript = '';

    // Reading
    public string $passageHtml = '';
    public string $passageSubtitle = '';

    // Writing / Speaking
    public string $promptHtml = '';
    public $image;                  // upload
    public ?string $imagePath = null;
    public ?int $taskNumber = null;     // writing
    public ?int $partNumber = null;     // speaking
    public string $cueCard = '';        // speaking
    public ?int $minWords = null;       // writing

    /**
     * Group structure (in-memory, persisted on save):
     *   [
     *     'id' => ?int,
     *     'question_type' => 'mcq_single',
     *     'instructions' => '',
     *     'shared_data' => "Heading 1\nHeading 2\n...",   // textarea-friendly
     *     'questions' => [ ['id'=>?, 'q_number'=>1, 'prompt'=>'', 'options'=>[['key'=>'A','text'=>'']], 'answers'=>'A|alt'], ... ],
     *   ]
     */
    public array $groups = [];

    public function mount(?int $item = null)
    {
        if ($item) {
            $bank = QuestionBankItem::with('groups.questions')->findOrFail($item);
            $this->itemId          = $bank->id;
            $this->module          = $bank->module;
            $this->title           = $bank->title;
            $this->audioPath       = $bank->audio_path;
            $this->transcript      = (string) $bank->transcript;
            $this->passageHtml     = (string) $bank->passage_html;
            $this->passageSubtitle = (string) $bank->passage_subtitle;
            $this->promptHtml      = (string) $bank->prompt_html;
            $this->imagePath       = $bank->image_path;

            $meta = $bank->meta_json ?? [];
            $this->taskNumber = $meta['task_number'] ?? null;
            $this->partNumber = $meta['part_number'] ?? null;
            $this->cueCard    = $meta['cue_card'] ?? '';
            $this->minWords   = $meta['min_words'] ?? null;

            $this->groups = $bank->groups->map(function (QuestionGroup $g) {
                return [
                    'id' => $g->id,
                    'question_type' => $g->question_type,
                    'instructions'  => (string) $g->instructions,
                    'shared_data'   => is_array($g->shared_data_json)
                        ? implode("\n", $g->shared_data_json)
                        : '',
                    'questions' => $g->questions->map(fn (Question $q) => [
                        'id'       => $q->id,
                        'q_number' => $q->q_number,
                        'prompt'   => (string) $q->prompt,
                        'options'  => $q->options_json ?? [],
                        'answers'  => is_array($q->correct_answers_json)
                            ? implode('|', $q->correct_answers_json)
                            : '',
                    ])->toArray(),
                ];
            })->toArray();
        } else {
            $this->module = request()->query('module', 'reading');
            if (!in_array($this->module, IeltsTypes::MODULES, true)) {
                $this->module = 'reading';
            }
        }
    }

    public function getQuestionTypesProperty(): array
    {
        return IeltsTypes::questionTypes($this->module);
    }

    public function addGroup(): void
    {
        $defaultType = array_key_first($this->questionTypes) ?? 'short_answer';
        $this->groups[] = [
            'id' => null,
            'question_type' => $defaultType,
            'instructions'  => '',
            'shared_data'   => '',
            'questions'     => [],
        ];
    }

    public function removeGroup(int $idx): void
    {
        if (isset($this->groups[$idx]['id'])) {
            QuestionGroup::find($this->groups[$idx]['id'])?->delete();
        }
        unset($this->groups[$idx]);
        $this->groups = array_values($this->groups);
    }

    public function addQuestion(int $gIdx): void
    {
        $next = (int) (collect($this->groups[$gIdx]['questions'])->max('q_number') ?? 0) + 1;
        $type = $this->groups[$gIdx]['question_type'];

        $defaultOptions = match ($type) {
            'tfng' => [['key' => 'TRUE', 'text' => 'True'], ['key' => 'FALSE', 'text' => 'False'], ['key' => 'NG', 'text' => 'Not Given']],
            'ynng' => [['key' => 'YES', 'text' => 'Yes'], ['key' => 'NO', 'text' => 'No'], ['key' => 'NG', 'text' => 'Not Given']],
            'mcq_single', 'mcq_multi' => [['key' => 'A', 'text' => ''], ['key' => 'B', 'text' => ''], ['key' => 'C', 'text' => ''], ['key' => 'D', 'text' => '']],
            default => [],
        };

        $this->groups[$gIdx]['questions'][] = [
            'id'       => null,
            'q_number' => $next,
            'prompt'   => '',
            'options'  => $defaultOptions,
            'answers'  => '',
        ];
    }

    public function removeQuestion(int $gIdx, int $qIdx): void
    {
        if (isset($this->groups[$gIdx]['questions'][$qIdx]['id'])) {
            Question::find($this->groups[$gIdx]['questions'][$qIdx]['id'])?->delete();
        }
        unset($this->groups[$gIdx]['questions'][$qIdx]);
        $this->groups[$gIdx]['questions'] = array_values($this->groups[$gIdx]['questions']);
    }

    public function addOption(int $gIdx, int $qIdx): void
    {
        $existing = $this->groups[$gIdx]['questions'][$qIdx]['options'] ?? [];
        $nextLetter = chr(ord('A') + count($existing));
        $existing[] = ['key' => $nextLetter, 'text' => ''];
        $this->groups[$gIdx]['questions'][$qIdx]['options'] = $existing;
    }

    public function removeOption(int $gIdx, int $qIdx, int $oIdx): void
    {
        unset($this->groups[$gIdx]['questions'][$qIdx]['options'][$oIdx]);
        $this->groups[$gIdx]['questions'][$qIdx]['options'] =
            array_values($this->groups[$gIdx]['questions'][$qIdx]['options']);
    }

    public function save()
    {
        $this->validate([
            'title'  => 'required|string|max:255',
            'module' => 'required|in:listening,reading,writing,speaking',
            'audio'  => 'nullable|file|mimes:mp3,wav,m4a,ogg|max:51200',
            'image'  => 'nullable|image|max:8192',
        ]);

        $meta = [];
        if ($this->module === 'writing') {
            $meta['task_number'] = (int) ($this->taskNumber ?? 1);
            $meta['min_words']   = (int) ($this->minWords ?? ($this->taskNumber == 2 ? 250 : 150));
        }
        if ($this->module === 'speaking') {
            $meta['part_number'] = (int) ($this->partNumber ?? 1);
            if ($this->cueCard) {
                $meta['cue_card'] = $this->cueCard;
            }
        }

        // File uploads
        if ($this->audio) {
            $this->audioPath = $this->audio->store('ielts/audio', 'public');
        }
        if ($this->image) {
            $this->imagePath = $this->image->store('ielts/images', 'public');
        }

        $payload = [
            'module'            => $this->module,
            'title'             => $this->title,
            'audio_path'        => $this->audioPath,
            'transcript'        => $this->transcript ?: null,
            'passage_html'      => $this->passageHtml ?: null,
            'passage_subtitle'  => $this->passageSubtitle ?: null,
            'prompt_html'       => $this->promptHtml ?: null,
            'image_path'        => $this->imagePath,
            'meta_json'         => $meta ?: null,
            'created_by'        => Auth::id(),
        ];

        $bank = $this->itemId
            ? tap(QuestionBankItem::findOrFail($this->itemId))->update($payload)
            : QuestionBankItem::create($payload);

        $this->itemId = $bank->id;

        // Persist groups + questions
        foreach ($this->groups as $gIdx => $g) {
            $sharedData = array_values(array_filter(array_map('trim', explode("\n", (string) $g['shared_data']))));

            $group = $g['id']
                ? tap(QuestionGroup::findOrFail($g['id']))->update([
                    'order_index'      => $gIdx,
                    'question_type'    => $g['question_type'],
                    'instructions'     => $g['instructions'] ?: null,
                    'shared_data_json' => $sharedData ?: null,
                ])
                : QuestionGroup::create([
                    'item_id'          => $bank->id,
                    'order_index'      => $gIdx,
                    'question_type'    => $g['question_type'],
                    'instructions'     => $g['instructions'] ?: null,
                    'shared_data_json' => $sharedData ?: null,
                ]);

            $this->groups[$gIdx]['id'] = $group->id;

            foreach (($g['questions'] ?? []) as $qIdx => $q) {
                $answers = array_values(array_filter(array_map('trim', explode('|', (string) $q['answers']))));
                $payloadQ = [
                    'q_number'             => (int) $q['q_number'],
                    'prompt'               => $q['prompt'] ?: null,
                    'options_json'         => !empty($q['options']) ? array_values($q['options']) : null,
                    'correct_answers_json' => $answers ?: null,
                ];

                if ($q['id']) {
                    Question::findOrFail($q['id'])->update($payloadQ);
                } else {
                    $created = Question::create(array_merge($payloadQ, ['group_id' => $group->id]));
                    $this->groups[$gIdx]['questions'][$qIdx]['id'] = $created->id;
                }
            }
        }

        $this->audio = null;
        $this->image = null;

        session()->flash('status', 'Saved.');
        return redirect()->route('admin.question_bank.edit', $bank);
    }

    public function render()
    {
        return view('livewire.pages.admin.question-bank.edit', [
            'questionTypes' => $this->questionTypes,
        ])->layout('layouts.app');
    }
}
