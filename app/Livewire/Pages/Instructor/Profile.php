<?php

namespace App\Livewire\Pages\Instructor;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $achieved_band;
    public $exam_date;
    public $profile_photo;
    public $currentPhoto;

    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'achieved_band' => 'nullable|numeric|min:0|max:9',
            'exam_date' => 'nullable|date',
            'profile_photo' => 'nullable|image|max:2048',
        ];
    }

    protected function passwordRules(): array
    {
        return [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->achieved_band = $user->target_band;
        $this->exam_date = $user->preferred_test_date?->format('Y-m-d');
        $this->currentPhoto = $user->profile_photo;
    }

    public function updateProfile(): void
    {
        $this->validate();

        $user = Auth::user();
        $photoPath = $user->profile_photo;

        if ($this->profile_photo) {
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $this->profile_photo->store('profile-photos', 'public');
        }

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'target_band' => $this->achieved_band,
            'preferred_test_date' => $this->exam_date,
            'profile_photo' => $photoPath,
        ]);

        $this->currentPhoto = $photoPath;
        $this->profile_photo = null;

        session()->flash('message', 'Profile updated successfully.');
    }

    public function removePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update(['profile_photo' => null]);
        $this->currentPhoto = null;
        $this->profile_photo = null;

        session()->flash('message', 'Profile photo removed.');
    }

    public function updatePassword(): void
    {
        $this->validate($this->passwordRules());

        $user = Auth::user();

        if (!Hash::check((string) $this->current_password, (string) $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Current password is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make((string) $this->new_password),
        ]);

        $this->current_password = null;
        $this->new_password = null;
        $this->new_password_confirmation = null;

        session()->flash('password_message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.pages.instructor.profile')->layout('layouts.app');
    }
}
