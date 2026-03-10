<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
class Login extends Component
{
    public $email;
    public $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            if ($user->is_blocked) {
                Auth::logout();
                $this->addError('email', 'Your account is blocked. Please contact admin.');
                return;
            }

            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('instructor')) {
                if ($user->instructor_status !== 'approved') {
                    return redirect()->intended(route('instructor.verification.pending'));
                }
                return redirect()->intended(route('instructor.dashboard'));
            }

            return redirect()->intended(route('student.dashboard'));
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.auth');
    }
}
