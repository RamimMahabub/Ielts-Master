<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\Auth;

class TestHistory extends Component
{
    public $attempts;

    public function mount()
    {
        $this->attempts = TestAttempt::where('user_id', Auth::id())
            ->with('mockTest')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.student.test-history')->layout('layouts.app');
    }
}
