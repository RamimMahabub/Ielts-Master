<?php

namespace App\Livewire\Pages\Student;

use App\Support\StudentSmartFeatures;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SmartPractice extends Component
{
    public array $weaknessReport = [];
    public $recommendations;

    public function mount(): void
    {
        $userId = Auth::id();

        $this->weaknessReport = StudentSmartFeatures::weaknessReport($userId);
        $this->recommendations = StudentSmartFeatures::recommendations($userId, 16);
    }

    public function render()
    {
        return view('livewire.pages.student.smart-practice')->layout('layouts.app');
    }
}
