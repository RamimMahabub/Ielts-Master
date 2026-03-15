<?php

namespace App\Livewire\Pages\Student;

use App\Models\TestAttempt;
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
    public $phone;
    public $country;
    public $timezone;
    public $exam_type;
    public $study_goal;
    public $daily_study_minutes;
    public $bio;
    public $target_band;
    public $preferred_test_date;
    public $profile_photo;
    public $currentPhoto;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public $profileCompletion = 0;
    public $daysUntilTest;
    public $averageScore = 0;
    public $targetGap;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|timezone',
            'exam_type' => 'nullable|in:academic,general',
            'study_goal' => 'nullable|string|max:1000',
            'daily_study_minutes' => 'nullable|integer|min:0|max:600',
            'bio' => 'nullable|string|max:500',
            'target_band' => 'nullable|numeric|min:0|max:9',
            'preferred_test_date' => 'nullable|date|after_or_equal:today',
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

    public function mount()
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->phone = $user->phone;
        $this->country = $user->country;
        $this->timezone = $user->timezone ?: config('app.timezone', 'UTC');
        $this->exam_type = $user->exam_type;
        $this->study_goal = $user->study_goal;
        $this->daily_study_minutes = $user->daily_study_minutes;
        $this->bio = $user->bio;
        $this->target_band = $user->target_band;
        $this->preferred_test_date = $user->preferred_test_date?->format('Y-m-d');
        $this->currentPhoto = $user->profile_photo;

        $this->refreshProfileMetrics();
    }

    public function updatedTargetBand(): void
    {
        if ($this->target_band === null || $this->target_band === '') {
            return;
        }

        if (!$this->isValidBandStep((float) $this->target_band)) {
            $this->addError('target_band', 'Target band must be in 0.5 increments (e.g. 6.0, 6.5, 7.0).');
        }
    }

    public function updateProfile()
    {
        $this->validate();

        if ($this->target_band !== null && $this->target_band !== '' && !$this->isValidBandStep((float) $this->target_band)) {
            throw ValidationException::withMessages([
                'target_band' => 'Target band must be in 0.5 increments (e.g. 6.0, 6.5, 7.0).',
            ]);
        }

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
            'phone' => $this->phone,
            'country' => $this->country,
            'timezone' => $this->timezone,
            'exam_type' => $this->exam_type,
            'study_goal' => $this->study_goal,
            'daily_study_minutes' => $this->daily_study_minutes,
            'bio' => $this->bio,
            'target_band' => $this->target_band,
            'preferred_test_date' => $this->preferred_test_date,
            'profile_photo' => $photoPath,
        ]);

        $this->currentPhoto = $photoPath;
        $this->profile_photo = null;
        $this->refreshProfileMetrics();

        session()->flash('message', 'Profile information updated successfully.');
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
        $this->refreshProfileMetrics();

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

    public function getTimezonesProperty(): array
    {
        return [
            'UTC',
            'Asia/Dhaka',
            'Asia/Kolkata',
            'Asia/Karachi',
            'Europe/London',
            'America/New_York',
            'Australia/Sydney',
        ];
    }

    private function refreshProfileMetrics(): void
    {
        $user = Auth::user();

        $this->profileCompletion = $this->calculateProfileCompletion($user);
        $this->daysUntilTest = $user->preferred_test_date ? now()->startOfDay()->diffInDays($user->preferred_test_date, false) : null;

        $this->averageScore = (float) (TestAttempt::where('user_id', $user->id)->avg('raw_score') ?? 0);
        $this->targetGap = is_numeric($user->target_band) ? round((float) $user->target_band - $this->averageScore, 1) : null;
    }

    private function calculateProfileCompletion($user): int
    {
        $fields = [
            $user->name,
            $user->phone,
            $user->country,
            $user->timezone,
            $user->exam_type,
            $user->study_goal,
            $user->daily_study_minutes,
            $user->bio,
            $user->target_band,
            $user->preferred_test_date,
            $user->profile_photo,
        ];

        $filled = collect($fields)->filter(function ($value) {
            return $value !== null && $value !== '';
        })->count();

        return (int) round(($filled / count($fields)) * 100);
    }

    private function isValidBandStep(float $band): bool
    {
        return abs(fmod($band * 10, 5.0)) < 0.00001;
    }

    public function render()
    {
        return view('livewire.pages.student.profile')->layout('layouts.app');
    }
}
