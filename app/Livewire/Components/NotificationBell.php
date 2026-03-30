<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public function getNotificationsProperty()
    {
        return Auth::user()?->unreadNotifications ?? collect();
    }

    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->dispatch('notificationRead'); // For cross-component updates if needed
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
