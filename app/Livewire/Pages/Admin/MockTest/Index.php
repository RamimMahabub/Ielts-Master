<?php

namespace App\Livewire\Pages\Admin\MockTest;

use App\Models\MockTest;
use App\Support\StudentNotificationDispatcher;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search'];

    public function delete(int $id): void
    {
        MockTest::findOrFail($id)->delete();
        session()->flash('status', 'Mock test deleted.');
    }

    public function togglePublish(int $id): void
    {
        $m = MockTest::findOrFail($id);
        $m->is_published = !$m->is_published;
        $m->save();

        if ($m->is_published) {
            StudentNotificationDispatcher::mockTestPublished($m);
            session()->flash('status', 'Mock test published and students notified.');
            return;
        }

        session()->flash('status', 'Mock test unpublished.');
    }

    public function render()
    {
        $tests = MockTest::query()
            ->withCount('modules')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.pages.admin.mock-test.index', compact('tests'))->layout('layouts.app');
    }
}
