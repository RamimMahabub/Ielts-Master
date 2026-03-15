<?php

namespace App\Livewire\Pages\Instructor;

use Livewire\Component;
use App\Models\User;

class VerificationPanel extends Component
{
    public function approve(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['instructor_status' => 'approved', 'is_blocked' => false]);
        $user->syncRoles(['instructor']);
        session()->flash('message', 'Instructor approved.');
    }

    public function reject(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['instructor_status' => 'rejected']);
        session()->flash('message', 'Instructor request rejected.');
    }

    public function render()
    {
        return view('livewire.pages.instructor.verification-panel', [
            'pendingInstructors' => User::role('instructor')
                ->where('instructor_status', 'pending')
                ->latest()
                ->get(),
        ])->layout('layouts.app');
    }
}
