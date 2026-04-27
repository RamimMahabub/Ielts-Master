<?php

namespace App\Livewire\Pages\Admin\QuestionBank;

use App\Models\QuestionBankItem;
use App\Support\IeltsTypes;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $module = '';
    public string $search = '';

    protected $queryString = ['module', 'search'];

    public function updatingModule()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $item = QuestionBankItem::findOrFail($id);
        // Optional: allow only owner or admin
        $item->delete();
        session()->flash('status', 'Deleted.');
    }

    public function render()
    {
        $items = QuestionBankItem::query()
            ->when($this->module, fn ($q) => $q->where('module', $this->module))
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.pages.admin.question-bank.index', [
            'items'   => $items,
            'modules' => IeltsTypes::MODULES,
        ])->layout('layouts.app');
    }
}
