<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\MockTest;

class MockTests extends Component
{
    public $availableTests;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
        $this->availableTests = MockTest::where('is_published', true)
            ->with('modules')
            ->latest()
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.student.mock-tests')->layout('layouts.app');
    }
}
