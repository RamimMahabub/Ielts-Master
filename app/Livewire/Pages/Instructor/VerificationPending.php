<?php

namespace App\Livewire\Pages\Instructor;

use Livewire\Component;

class VerificationPending extends Component
{
    public function render()
    {
        return view('livewire.pages.instructor.verification-pending')->layout('layouts.app');
    }
}
