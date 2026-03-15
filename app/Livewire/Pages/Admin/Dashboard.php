<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\MockTest;
use App\Models\TestAttempt;

class Dashboard extends Component
{
    public $totalUsers;
    public $totalTests;
    public $totalAttempts;
    public $recentAttempts;
    public $pendingInstructorRequests = 0;

    public function mount()
    {
        $this->totalUsers = User::count();
        $this->totalTests = MockTest::count();
        $this->totalAttempts = TestAttempt::count();
        $this->pendingInstructorRequests = User::role('instructor')
            ->where('instructor_status', 'pending')
            ->count();
        $this->recentAttempts = TestAttempt::with(['user', 'mockTest'])
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.admin.dashboard')->layout('layouts.app');
    }
}
