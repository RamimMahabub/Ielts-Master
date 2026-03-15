<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use App\Models\User;

class UserManagement extends Component
{
    public $users;
    public $search = '';

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $this->users = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function toggleBlock(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_blocked' => !$user->is_blocked]);
        $this->loadUsers();
    }

    public function assignInstructor(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->syncRoles(['instructor']);
        $user->update(['instructor_status' => 'approved']);
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.pages.admin.user-management')->layout('layouts.app');
    }
}
