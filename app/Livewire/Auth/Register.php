<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class Register extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = 'student';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:student,instructor',
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'instructor_status' => $this->role === 'instructor' ? 'pending' : 'none',
            'is_blocked' => false,
        ]);

        Role::findOrCreate($this->role);
        $user->assignRole($this->role);
        Auth::login($user);

        if ($this->role === 'instructor') {
            return redirect()->route('instructor.verification.pending');
        }

        return redirect()->route('student.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.auth');
    }
}
