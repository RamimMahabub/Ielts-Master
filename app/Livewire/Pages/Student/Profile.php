<?php

namespace App\Livewire\Pages\Student;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    use WithFileUploads;

    public $name;
    public $target_band;
    public $preferred_test_date;
    public $profile_photo;
    public $currentPhoto;

    protected $rules = [
        'name' => 'required|string|max:255',
        'target_band' => 'nullable|string|max:10',
        'preferred_test_date' => 'nullable|date',
        'profile_photo' => 'nullable|image|max:2048',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->target_band = $user->target_band;
        $this->preferred_test_date = $user->preferred_test_date;
        $this->currentPhoto = $user->profile_photo;
    }

    public function updateProfile()
    {
        $this->validate();

        $user = Auth::user();
        $photoPath = $user->profile_photo;

        if ($this->profile_photo) {
            $photoPath = $this->profile_photo->store('profile-photos', 'public');
        }

        $user->update([
            'name' => $this->name,
            'target_band' => $this->target_band,
            'preferred_test_date' => $this->preferred_test_date,
            'profile_photo' => $photoPath,
        ]);

        $this->currentPhoto = $photoPath;
        $this->profile_photo = null;

        session()->flash('message', 'Profile successfully updated.');
    }

    public function render()
    {
        return view('livewire.pages.student.profile')->layout('layouts.app');
    }
}
