<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use App\Models\MockTest;
use App\Models\ContentAsset;
use App\Models\User;
use App\Notifications\NewMockTestPublished;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class MockTestBuilder extends Component
{
    public $title = '';
    public $duration_minutes = 150;
    public $section_type = 'reading';
    public $selectedAssets = [];
    public $availableAssets;
    public $existingTests;

    protected $rules = [
        'title' => 'required|string|max:255',
        'duration_minutes' => 'required|integer|min:30',
        'section_type' => 'required|in:reading,listening,writing,speaking',
        'selectedAssets' => 'required|array|min:1',
    ];

    public function mount()
    {
        if (Auth::user()?->hasRole('instructor') && Auth::user()?->instructor_status !== 'approved') {
            abort(403);
        }

        $this->availableAssets = ContentAsset::latest()->get();
        $this->loadTests();
    }

    public function createTest()
    {
        $this->validate();

        $test = MockTest::create([
            'title' => $this->title,
            'duration_minutes' => $this->duration_minutes,
            'is_published' => false,
            'created_by' => Auth::id(),
        ]);

        $section = $test->sections()->create([
            'section_type' => $this->section_type,
            'order_index' => 1,
        ]);

        foreach ($this->selectedAssets as $index => $assetId) {
            $section->items()->create([
                'asset_id' => $assetId,
                'order_index' => $index + 1,
            ]);
        }

        $this->reset(['title', 'selectedAssets']);
        $this->duration_minutes = 150;
        $this->section_type = 'reading';
        $this->loadTests();

        session()->flash('message', 'Mock test created.');
    }

    public function togglePublish(int $testId): void
    {
        $test = MockTest::findOrFail($testId);
        $test->update(['is_published' => !$test->is_published]);

        if ($test->is_published) {
            $students = User::role('student')->get();
            Notification::send($students, new NewMockTestPublished($test));
        }

        $this->loadTests();
    }

    public function loadTests(): void
    {
        $this->existingTests = MockTest::with('sections.items.asset', 'creator')->latest()->get();
    }

    public function render()
    {
        return view('livewire.pages.admin.mock-test-builder')->layout('layouts.app');
    }
}
