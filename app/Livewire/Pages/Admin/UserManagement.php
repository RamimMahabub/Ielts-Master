<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use App\Models\User;

class UserManagement extends Component
{
    public $users;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $showEditModal = false;
    public $showDeleteModal = false;
    public $editingUserId = null;
    public $deletingUserId = null;
    
    public $editName = '';
    public $editEmail = '';
    public $editRole = '';
    public $editIsBlocked = false;

    public function mount()
    {
        $this->loadUsers();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadUsers();
    }

    public function loadUsers()
    {
        $query = User::with('roles')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
            
        if ($this->sortField === 'role') {
            $users = $query->get();
            if ($this->sortDirection === 'asc') {
                $this->users = $users->sortBy(function($user) { 
                    return $user->getRoleNames()->first() ?? ''; 
                })->values();
            } else {
                $this->users = $users->sortByDesc(function($user) { 
                    return $user->getRoleNames()->first() ?? ''; 
                })->values();
            }
        } else {
            $this->users = $query->orderBy($this->sortField, $this->sortDirection)->get();
        }
    }

    public function updatedSearch()
    {
        $this->loadUsers();
    }

    public function toggleBlock(int $userId): void
    {
        $user = User::findOrFail($userId);
        
        // Prevent an admin from being blocked
        if ($user->hasRole('admin')) {
            return;
        }

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

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editRole = $user->getRoleNames()->first() ?? '';
        $this->editIsBlocked = (bool)$user->is_blocked;
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $this->editingUserId,
            'editRole' => 'required|string',
        ]);

        $user = User::findOrFail($this->editingUserId);
        
        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'is_blocked' => $user->hasRole('admin') ? false : $this->editIsBlocked,
        ]);

        if ($this->editRole !== '') {
            $user->syncRoles([$this->editRole]);
            
            if ($this->editRole === 'instructor' && $user->instructor_status !== 'approved') {
                 $user->update(['instructor_status' => 'approved']);
            } elseif (in_array($this->editRole, ['student', 'admin'])) {
                 $user->update(['instructor_status' => 'none']);
            }
        }

        $this->showEditModal = false;
        $this->loadUsers();
        session()->flash('message', 'User updated successfully.');
    }

    public function confirmDelete($userId)
    {
        $user = User::findOrFail($userId);
        if ($user->hasRole('admin')) {
            return;
        }
        $this->deletingUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        $user = User::find($this->deletingUserId);
        if ($user && !$user->hasRole('admin')) {
            $user->delete();
        }
        $this->showDeleteModal = false;
        $this->loadUsers();
        session()->flash('message', 'User deleted successfully.');
    }

    public function render()
    {
        return view('livewire.pages.admin.user-management')->layout('layouts.app');
    }
}
