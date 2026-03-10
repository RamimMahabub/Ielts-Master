<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use App\Models\ContentAsset;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionGroup;
use Illuminate\Support\Facades\Auth;

class QuestionBankList extends Component
{
    public $assets;
    public $search = '';
    public $filterType = 'all';

    public $selectedAssetId;
    public $title = '';
    public $type = 'reading_passage';
    public $body_text = '';
    public $instructions = '';
    public $question_type = 'short_answer';
    public $q_no = 1;
    public $prompt = '';
    public $answer_text = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'type' => 'required|in:reading_passage,listening_audio,writing_task,speaking_part',
        'instructions' => 'required|string|max:1000',
        'question_type' => 'required|string|max:100',
        'q_no' => 'required|integer|min:1',
        'prompt' => 'required|string|max:1000',
        'answer_text' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        if (Auth::user()?->hasRole('instructor') && Auth::user()?->instructor_status !== 'approved') {
            abort(403);
        }

        $this->loadAssets();
    }

    public function loadAssets()
    {
        $query = ContentAsset::with('questionGroups.questions.answers');

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        $this->assets = $query->latest()->get();
    }

    public function saveAsset(): void
    {
        $this->validate();

        $asset = ContentAsset::updateOrCreate(
            ['id' => $this->selectedAssetId],
            [
                'title' => $this->title,
                'type' => $this->type,
                'body_text' => $this->body_text,
                'transcript_text' => $this->type === 'listening_audio' ? $this->body_text : null,
            ]
        );

        $group = QuestionGroup::firstOrCreate(
            [
                'asset_id' => $asset->id,
                'question_type' => $this->question_type,
                'start_no' => $this->q_no,
                'end_no' => $this->q_no,
            ],
            ['instructions' => $this->instructions]
        );

        $question = Question::updateOrCreate(
            ['group_id' => $group->id, 'q_no' => $this->q_no],
            ['prompt' => $this->prompt]
        );

        if (!empty($this->answer_text)) {
            QuestionAnswer::updateOrCreate(
                ['question_id' => $question->id],
                ['answer_text' => $this->answer_text]
            );
        }

        $this->resetForm();
        $this->loadAssets();
        session()->flash('message', 'Question bank entry saved.');
    }

    public function editAsset(int $assetId): void
    {
        $asset = ContentAsset::with('questionGroups.questions.answers')->findOrFail($assetId);
        $this->selectedAssetId = $asset->id;
        $this->title = $asset->title;
        $this->type = $asset->type;
        $this->body_text = (string) ($asset->body_text ?? '');

        $group = $asset->questionGroups->first();
        if ($group) {
            $this->instructions = (string) $group->instructions;
            $this->question_type = $group->question_type;
            $question = $group->questions->first();
            if ($question) {
                $this->q_no = $question->q_no;
                $this->prompt = $question->prompt;
                $this->answer_text = (string) optional($question->answers->first())->answer_text;
            }
        }
    }

    public function deleteAsset(int $assetId): void
    {
        ContentAsset::where('id', $assetId)->delete();
        if ($this->selectedAssetId === $assetId) {
            $this->resetForm();
        }
        $this->loadAssets();
    }

    public function resetForm(): void
    {
        $this->selectedAssetId = null;
        $this->title = '';
        $this->type = 'reading_passage';
        $this->body_text = '';
        $this->instructions = '';
        $this->question_type = 'short_answer';
        $this->q_no = 1;
        $this->prompt = '';
        $this->answer_text = '';
    }

    public function updatedSearch()
    {
        $this->loadAssets();
    }

    public function updatedFilterType()
    {
        $this->loadAssets();
    }

    public function render()
    {
        return view('livewire.pages.admin.question-bank-list')->layout('layouts.app');
    }
}
