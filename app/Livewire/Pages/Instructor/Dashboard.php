<?php

namespace App\Livewire\Pages\Instructor;

use Livewire\Component;
use App\Models\TestAttempt;
use App\Models\MockTest;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $evaluationsPending = 0;
    public $myTests = 0;
    public $publishedTests;

    public function mount()
    {
        abort_if(Auth::user()?->instructor_status !== 'approved', 403);
        $this->evaluationsPending = TestAttempt::where('status', 'pending_evaluation')->count();
        $this->myTests = MockTest::where('created_by', Auth::id())->count();
        $this->publishedTests = MockTest::where('is_published', true)->latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.pages.instructor.dashboard')->layout('layouts.app');
    }
}
